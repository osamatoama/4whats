<?php

namespace App\Jobs\Salla\Pull\Customers;

use App\Jobs\Concerns\InteractsWithBatches;
use App\Jobs\Concerns\InteractsWithException;
use App\Services\Salla\Merchant\SallaMerchantException;
use App\Services\Salla\Merchant\SallaMerchantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SallaPullCustomersPerPageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithBatches, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $accessToken,
        public int $userId,
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
            $response = $service->customers()->get(page: $this->page);
        } catch (SallaMerchantException $e) {
            $this->handleException(
                e: new SallaMerchantException(
                    message: "Exception while pulling customers from salla | User: $this->userId | Page: $this->page | Message: {$e->getMessage()}",
                    code: $e->getCode(),
                ),
            );

            return;
        }

        $jobs = [];
        foreach ($response['data'] as $customer) {
            $jobs[] = new SallaPullCustomerJob(
                userId: $this->userId,
                data: $customer,
            );
        }
        $this->addOrCreateBatch(jobs: $jobs, name: 'salla.pull.customers:'.$this->userId);
    }
}
