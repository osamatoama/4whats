<?php

namespace App\Jobs\Salla\Webhook\App\Store;

use App\Dto\TokenDto;
use App\Enums\Jobs\BatchName;
use App\Enums\Jobs\QueueName;
use App\Jobs\Salla\Installation\InstallAppJob;
use App\Models\Store;
use App\Services\Queue\BatchService;
use App\Services\Token\TokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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

            if ($store->is_uninstalled) {
                $store->update(
                    attributes: [
                        'is_uninstalled' => false,
                    ],
                );

                $store->user()->update(
                    values: [
                        'is_uninstalled' => false,
                    ],
                );
            }

            return;
        }

        BatchService::createPendingBatch(
            jobs: new InstallAppJob(
                sallaToken: $this->data,
            ),
            batchName: BatchName::SALLA_INSTALLATION,
            storeId: $this->merchantId,
            deleteWhenFinished: true,
            queue: QueueName::SUBSCRIPTIONS->value
        )->dispatch();
    }
}
