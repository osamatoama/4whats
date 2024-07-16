<?php

namespace App\Jobs\Zid\Installation;

use App\Dto\StoreDto;
use App\Dto\TokenDto;
use App\Dto\UserDto;
use App\Dto\WhatsappAccountDto;
use App\Dto\WidgetDto;
use App\Enums\Jobs\BatchName;
use App\Enums\MessageTemplate;
use App\Enums\UserRole;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Jobs\Zid\Pull\AbandonedCarts\PullAbandonedCartsJob;
use App\Jobs\Zid\Pull\Customers\PullCustomersJob;
use App\Jobs\Zid\Pull\OrderStatuses\PullOrderStatusesJob;
use App\Models\Store;
use App\Services\OAuth\OAuthService;
use App\Services\Queue\BatchService;
use App\Services\Setting\SettingService;
use App\Services\Store\StoreService;
use App\Services\Template\TemplateService;
use App\Services\Token\TokenService;
use App\Services\WhatsappAccount\WhatsappAccountService;
use App\Services\Widget\WidgetService;
use App\Services\Zid\OAuth\Support\Token;
use App\Services\Zid\OAuth\Support\User;
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
        public readonly User $zidUser,
        public readonly Token $zidToken,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $oauthService = new OAuthService();
        $password = $oauthService->generatePassword(
            isTesting: Str::endsWith(
                haystack: $this->zidUser->email,
                needles: [
                    '@zam-partner.email',
                ],
            ),
        );
        $user = $oauthService->getOrCreateUser(
            userDto: UserDto::fromZid(
                zidUser: $this->zidUser,
                password: $password,
            ),
            role: UserRole::MERCHANT,
            password: $password,
        );

        $store = DB::transaction(
            callback: function () use ($user): Store {
                (new TokenService())->syncToken(
                    user: $user,
                    tokenDto: TokenDto::fromZid(
                        zidToken: $this->zidToken,
                    ),
                );

                $store = (new StoreService())->create(
                    storeDto: StoreDto::fromZid(
                        userId: $user->id,
                        zidStore: $this->zidUser->store,
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
                    messageTemplates: MessageTemplate::zidCases()->toArray(),
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
                $this->getWebhooksJobs(),
                [
                    new RegisterWidgetScriptJob(
                        managerToken: $this->zidToken->managerToken,
                        accessToken: $this->zidToken->accessToken,
                    ),
                ],
                [
                    new SendCredentialsJob(
                        user: $user,
                        password: $password,
                    ),
                ],
            ),
        )->dispatch();
    }

    protected function getStoreBatches(Store $store): array
    {
        return [
            BatchService::createPendingBatch(
                jobs: new PullCustomersJob(
                    managerToken: $this->zidToken->managerToken,
                    accessToken: $this->zidToken->accessToken,
                    storeId: $store->id,
                ),
                batchName: BatchName::ZID_PULL_CUSTOMERS,
                storeId: $store->id,
            ),
            BatchService::createPendingBatch(
                jobs: new PullAbandonedCartsJob(
                    managerToken: $this->zidToken->managerToken,
                    accessToken: $this->zidToken->accessToken,
                    storeId: $store->id,
                ),
                batchName: BatchName::ZID_PULL_ABANDONED_CARTS,
                storeId: $store->id,
            ),
            BatchService::createPendingBatch(
                jobs: new PullOrderStatusesJob(
                    storeId: $store->id,
                ),
                batchName: BatchName::ZID_PULL_ORDER_STATUSES,
                storeId: $store->id,
                finallyCallback: function (Batch $batch) use ($store): void {
                    SettingService::updateOrderStatusId(
                        store: $store,
                        orderStatuesId: $store->orderStatuses()->first()->id,
                    );
                }
            ),
        ];
    }

    protected function getWebhooksJobs(): array
    {
        $events = [
            'order.create',
            'order.status.update',
            'abandoned_cart.created',
            'abandoned_cart.completed',
            'customer.create',
            'customer.update',
        ];

        $jobs = [];
        foreach ($events as $event) {
            $jobs[] = new RegisterWebhookJob(
                managerToken: $this->zidToken->managerToken,
                accessToken: $this->zidToken->accessToken,
                providerId: $this->zidUser->store->id,
                event: $event,
            );
        }

        return $jobs;
    }
}
