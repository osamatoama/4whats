<?php

namespace App\Jobs\Salla\Installation;

use App\Dto\StoreDto;
use App\Dto\TokenDto;
use App\Dto\UserDto;
use App\Dto\WhatsappAccountDto;
use App\Dto\WidgetDto;
use App\Enums\Jobs\BatchName;
use App\Enums\MessageTemplate;
use App\Enums\UserRole;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Jobs\Salla\Pull\AbandonedCarts\SallaPullAbandonedCartsJob;
use App\Jobs\Salla\Pull\Customers\SallaPullCustomersJob;
use App\Jobs\Salla\Pull\OrderStatuses\SallaPullOrderStatusesJob;
use App\Models\Store;
use App\Services\OAuth\OAuthService;
use App\Services\Queue\BatchService;
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
use Illuminate\Support\Str;

class InstallAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithBatches, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly array $sallaToken,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $sallaOAuthService = new SallaOAuthService();
            $resourceOwner = $sallaOAuthService->getResourceOwner(
                accessToken: $this->sallaToken['access_token'],
            );

            $oauthService = new OAuthService();
            $password = $oauthService->generatePassword(
                isTesting: Str::endsWith(
                    haystack: $resourceOwner->getEmail(),
                    needles: [
                        '@email.partners',
                    ],
                ),
            );
            $user = $oauthService->getOrCreateUser(
                userDto: UserDto::fromSalla(
                    sallaUser: $resourceOwner,
                    password: $password,
                ),
                role: UserRole::MERCHANT,
                password: $password,
            );

            if ($user->is_uninstalled) {
                $user->update(
                    attributes: [
                        'is_uninstalled' => false,
                    ],
                );
            }

            $store = DB::transaction(
                callback: function () use ($resourceOwner, $user): Store {
                    (new TokenService())->syncToken(
                        user: $user,
                        tokenDto: TokenDto::fromSalla(
                            sallaToken: $this->sallaToken,
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
                            mobile: $store->mobile,
                        ),
                    );

                    (new TemplateService())->bulkCreate(
                        storeId: $store->id,
                        messageTemplates: MessageTemplate::sallaCases()->toArray(),
                    );

                    (new SettingService())->createDefaultSettings(
                        storeId: $store->id,
                    );

                    (new WhatsappAccountService())->create(
                        whatsappAccountDto: WhatsappAccountDto::fromExpired(
                            storeId: $store->id,
                            label: $store->name,
                        ),
                    );

                    return $store;
                },
            );

            Bus::chain(
                jobs: array_merge(
                    $this->getStoreBatches(store: $store),
                    [
                        new SendCredentialsJob(
                            user: $user,
                            password: $password,
                        ),
                    ],
                ),
            )->dispatch();
        } catch (Exception $e) {
            logger()->error(message: $e);

            $this->fail(
                exception: $e,
            );
        }
    }

    protected function getStoreBatches(Store $store): array
    {
        return [
            BatchService::createPendingBatch(
                jobs: new SallaPullCustomersJob(
                    accessToken: $this->sallaToken['access_token'],
                    storeId: $store->id,
                ),
                batchName: BatchName::SALLA_PULL_CUSTOMERS,
                storeId: $store->id,
            ),
            BatchService::createPendingBatch(
                jobs: new SallaPullAbandonedCartsJob(
                    accessToken: $this->sallaToken['access_token'],
                    storeId: $store->id,
                ),
                batchName: BatchName::SALLA_PULL_ABANDONED_CARTS,
                storeId: $store->id,
            ),
            BatchService::createPendingBatch(
                jobs: new SallaPullOrderStatusesJob(
                    accessToken: $this->sallaToken['access_token'],
                    storeId: $store->id,
                ),
                batchName: BatchName::SALLA_PULL_ORDER_STATUSES,
                storeId: $store->id,
                finallyCallback: function (Batch $batch) use ($store): void {
                    SettingService::updateOrderStatusId(
                        store: $store,
                        orderStatuesId: $store->orderStatuses()->first()->id,
                    );
                },
            ),
        ];
    }
}
