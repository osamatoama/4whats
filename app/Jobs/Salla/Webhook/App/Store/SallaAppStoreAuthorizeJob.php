<?php

namespace App\Jobs\Salla\Webhook\App\Store;

use App\Dto\StoreDto;
use App\Dto\TokenDto;
use App\Dto\UserDto;
use App\Dto\WhatsappAccountDto;
use App\Dto\WidgetDto;
use App\Enums\Jobs\BatchName;
use App\Enums\MessageTemplate;
use App\Enums\ProviderType;
use App\Enums\SettingKey;
use App\Enums\UserRole;
use App\Jobs\Salla\Pull\AbandonedCarts\SallaPullAbandonedCartsJob;
use App\Jobs\Salla\Pull\Customers\SallaPullCustomersJob;
use App\Jobs\Salla\Pull\OrderStatuses\SallaPullOrderStatusesJob;
use App\Models\Store;
use App\Services\OAuth\OAuthService;
use App\Services\Salla\OAuth\SallaOAuthService;
use App\Services\Setting\SettingService;
use App\Services\Store\StoreService;
use App\Services\Template\TemplateService;
use App\Services\Token\TokenService;
use App\Services\WhatsappAccount\WhatsappAccountService;
use App\Services\Widget\WidgetService;
use Exception;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class SallaAppStoreAuthorizeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $event,
        public int $merchantId,
        public array $data,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $store = Store::query()->salla(providerId: $this->merchantId)->first();
        if ($store !== null) {
            (new TokenService())->syncToken(
                user: $store->user,
                tokenDto: TokenDto::fromSalla(
                    sallaToken: $this->data,
                ),
            );

            return;
        }

        try {
            $sallaOAuthService = new SallaOAuthService();
            $resourceOwner = $sallaOAuthService->getResourceOwner(
                accessToken: $this->data['access_token'],
            );

            $oauthService = new OAuthService();
            $password = $oauthService->generatePassword();
            $user = $oauthService->getOrCreateUser(
                userDto: UserDto::fromSalla(
                    sallaUser: $resourceOwner,
                    password: $password,
                ),
                role: UserRole::MERCHANT,
                password: $password,
            );

            $store = DB::transaction(callback: function () use ($resourceOwner, $user): Store {
                (new TokenService())->syncToken(
                    user: $user,
                    tokenDto: TokenDto::fromSalla(
                        sallaToken: $this->data,
                    ),
                );

                $store = (new StoreService())->create(
                    storeDto: StoreDto::fromSalla(
                        userId: $user->id,
                        sallaStore: $resourceOwner->toArray(),
                    ),
                );

                (new WidgetService())->create(
                    widgetDto: WidgetDto::fromDefault(
                        storeId: $store->id,
                    ),
                );

                (new TemplateService())->bulkCreate(
                    storeId: $store->id,
                    messageTemplates: MessageTemplate::sallaCases()->toArray(),
                );

                (new SettingService())->createDefaultSettings(
                    storeId: $store->id,
                    providerType: ProviderType::SALLA,
                );

                (new WhatsappAccountService())->create(
                    whatsappAccountDto: WhatsappAccountDto::fromExpired(
                        storeId: $store->id,
                        label: $store->name,
                    ),
                );

                return $store;
            });

            $this->pullStoreData(store: $store);
        } catch (Exception $e) {
            logger()->error(message: $e);
        }
    }

    protected function pullStoreData(Store $store): void
    {
        $customersBatch = Bus::batch(
            jobs: new SallaPullCustomersJob(accessToken: $this->data['access_token'], storeId: $store->id),
        )->name(
            name: BatchName::SALLA_PULL_CUSTOMERS->generate(storeId: $store->id),
        );

        $abandonedCartsBatch = Bus::batch(
            jobs: new SallaPullAbandonedCartsJob(accessToken: $this->data['access_token'], storeId: $store->id),
        )->name(
            name: BatchName::SALLA_PULL_ABANDONED_CARTS->generate(storeId: $store->id),
        );

        $orderStatusesBatch = Bus::batch(
            jobs: new SallaPullOrderStatusesJob(accessToken: $this->data['access_token'], storeId: $store->id),
        )->name(
            name: BatchName::SALLA_PULL_ORDER_STATUSES->generate(storeId: $store->id),
        )->finally(callback: function (Batch $batch) use ($store): void {
            $store->settings()
                ->where(
                    column: 'key',
                    operator: '=',
                    value: SettingKey::STORE_SALLA_CUSTOM_REVIEW_ORDER,
                )
                ->update(values: [
                    'value' => $store->orderStatuses()->first()->id,
                ]);
        });

        Bus::chain(jobs: [
            $customersBatch,
            $abandonedCartsBatch,
            $orderStatusesBatch,
        ])->dispatch();
    }
}
