<?php

namespace App\Jobs\Zid\Pull\Customers;

use App\Enums\Jobs\BatchName;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Jobs\Concerns\InteractsWithException;
use App\Services\Zid\Merchant\ZidMerchantException;
use App\Services\Zid\Merchant\ZidMerchantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PullCustomersPerPageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithBatches, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $managerToken,
        public string $accessToken,
        public int $storeId,
        public int $page,
    ) {
        $this->maxAttempts = 5;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new ZidMerchantService(
            managerToken: $this->managerToken,
            accessToken: $this->accessToken,
        );

        try {
            $response = $service->customers()->get(
                page: $this->page,
            );
        } catch (ZidMerchantException $e) {
            $this->handleException(
                e: new ZidMerchantException(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Exception while pulling customers from zid',
                            "Store: {$this->storeId}",
                            "Page: {$this->page}",
                            "Reason: {$e->getMessage()}",
                        ],
                    ),
                    code: $e->getCode(),
                ),
            );

            return;
        }

        $jobs = [];
        foreach ($response['customers'] as $abandonedCart) {
            $jobs[] = new PullCustomerJob(
                storeId: $this->storeId,
                data: $abandonedCart,
            );
        }

        $this->addOrCreateBatch(
            jobs: $jobs,
            batchName: BatchName::ZID_PULL_CUSTOMERS,
            storeId: $this->storeId,
        );
    }
}
