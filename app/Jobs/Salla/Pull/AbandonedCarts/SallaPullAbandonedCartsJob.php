<?php

namespace App\Jobs\Salla\Pull\AbandonedCarts;

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

class SallaPullAbandonedCartsJob implements ShouldQueue
{
    use Batchable, Dispatchable, HandleException, InteractsWithQueue, Queueable, SerializesModels;

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
            $response = $service->abandonedCarts()->get();
        } catch (SallaMerchantException $e) {
            $this->handleException(
                e: new SallaMerchantException(
                    message: "Exception while pulling abandoned carts from salla | Store: $this->storeId | Message: {$e->getMessage()}",
                    code: $e->getCode(),
                ),
            );

            return;
        }

        $jobs = [];
        for ($page = 1, $totalPages = $response['pagination']['totalPages']; $page <= $totalPages; $page++) {
            $jobs[] = new SallaPullAbandonedCartsPerPageJob(
                accessToken: $this->accessToken,
                storeId: $this->storeId,
                page: $page,
            );
        }

        if ($this->batchId !== null) {
            $this->batch()->add(jobs: $jobs);
        } else {
            Bus::batch(jobs: [$jobs])->name(name: 'salla.pull.abandoned-carts:'.$this->storeId)->dispatch();
        }
    }
}
