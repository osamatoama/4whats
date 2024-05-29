<?php

namespace App\Jobs\Salla\Pull\OrderStatuses;

use App\Enums\Jobs\JobBatchName;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Jobs\Concerns\InteractsWithException;
use App\Services\Salla\Merchant\SallaMerchantException;
use App\Services\Salla\Merchant\SallaMerchantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SallaPullOrderStatusesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithBatches, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $accessToken,
        public int $storeId,
    ) {
        $this->maxAttempts = 5;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new SallaMerchantService(accessToken: $this->accessToken);

        try {
            $response = $service->orderStatuses()->get();
        } catch (SallaMerchantException $e) {
            $this->handleException(
                e: new SallaMerchantException(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Exception while pulling order statuses from salla',
                            "Store: {$this->storeId}",
                            "Reason: {$e->getMessage()}",
                        ],
                    ),
                    code: $e->getCode(),
                ),
            );

            return;
        }

        $jobs = [];
        foreach ($response['data'] as $orderStatus) {
            $jobs[] = new SallaPullOrderStatusJob(
                storeId: $this->storeId,
                data: $orderStatus,
            );
        }

        $this->addOrCreateBatch(
            jobs: $jobs,
            name: JobBatchName::SALLA_PULL_ORDER_STATUSES->generate(storeId: $this->storeId),
        );
    }
}
