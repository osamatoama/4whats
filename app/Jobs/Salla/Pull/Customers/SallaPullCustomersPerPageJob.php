<?php

namespace App\Jobs\Salla\Pull\Customers;

use App\Jobs\Concerns\HandleException;
use App\Services\Salla\Merchant\SallaMerchantException;
use App\Services\Salla\Merchant\SallaMerchantService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class SallaPullCustomersPerPageJob implements ShouldQueue
{
    use Batchable, Dispatchable, HandleException, InteractsWithQueue, Queueable, SerializesModels;

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
            $response = $service->customers()->get(page: $this->page);
        } catch (SallaMerchantException $e) {
            $this->handleException(
                e: new SallaMerchantException(message: "Exception while pulling orders from salla | Store: $this->storeId | Page: $this->page | Message: {$e->getMessage()}", code: $e->getCode()),
            );

            return;
        }

        $jobs = [];
        foreach ($response['data'] as $customer) {
            $jobs[] = new SallaPullCustomerJob(
                storeId: $this->storeId,
                data: $customer,
            );
        }

        if ($this->batchId !== null) {
            $this->batch()->add(jobs: $jobs);
        } else {
            Bus::batch(jobs: [$jobs])->name(name: 'salla.pull.customers:'.$this->storeId)->dispatch();
        }
    }
}
