<?php

namespace App\Jobs\Salla\Pull\Customers;

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

class SallaPullCustomersJob implements ShouldQueue
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
            $response = $service->customers()->get();
        } catch (SallaMerchantException $e) {
            $this->handleException(
                e: new SallaMerchantException(
                    message: "Exception while pulling customers from salla | Store: {$this->storeId} | Message: {$e->getMessage()}",
                    code: $e->getCode(),
                ),
            );

            return;
        }

        $jobs = [];
        for ($page = 1, $totalPages = $response['pagination']['totalPages']; $page <= $totalPages; $page++) {
            $jobs[] = new SallaPullCustomersPerPageJob(
                accessToken: $this->accessToken,
                storeId: $this->storeId,
                page: $page,
            );
        }

        $this->addOrCreateBatch(
            jobs: $jobs,
            name: JobBatchName::SALLA_PULL_CUSTOMERS->generate(storeId: $this->storeId),
        );
    }
}
