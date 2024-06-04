<?php

namespace App\Jobs\Salla\Pull\AbandonedCarts;

use App\Enums\Jobs\BatchName;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Jobs\Concerns\InteractsWithException;
use App\Services\Salla\Merchant\SallaMerchantException;
use App\Services\Salla\Merchant\SallaMerchantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SallaPullAbandonedCartsPerPageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithBatches, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $accessToken,
        public int $storeId,
        public int $page = 1,
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
            $response = $service->abandonedCarts()->get(page: $this->page);
        } catch (SallaMerchantException $e) {
            $this->handleException(
                e: new SallaMerchantException(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Exception while pulling abandoned carts from salla',
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
        foreach ($response['data'] as $customer) {
            $jobs[] = new SallaPullAbandonedCartJob(
                storeId: $this->storeId,
                data: $customer,
            );
        }

        $this->addOrCreateBatch(
            jobs: $jobs,
            batchName: BatchName::SALLA_PULL_ABANDONED_CARTS,
            storeId: $this->storeId,
        );
    }
}
