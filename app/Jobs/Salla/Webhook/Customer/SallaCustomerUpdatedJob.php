<?php

namespace App\Jobs\Salla\Webhook\Customer;

use App\Enums\ContactSource;
use App\Enums\ProviderType;
use App\Jobs\Concerns\InteractsWithException;
use App\Models\Store;
use App\Services\Salla\Merchant\SallaMerchantService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SallaCustomerUpdatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

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
        if ($store === null) {
            $this->handleException(
                e: new Exception(
                    message: "Error while handling salla customer updated webhook | Merchant: {$this->merchantId} | Message: Store not found",
                ),
                fail: true,
            );

            return;
        }

        $store->contacts()->updateOrCreate(attributes: [
            'provider_type' => ProviderType::SALLA,
            'provider_id' => $this->data['id'],
            'source' => ContactSource::SALLA,
        ], values: [
            'first_name' => $this->data['first_name'],
            'last_name' => $this->data['last_name'],
            'email' => $this->data['email'] ?: null,
            'mobile' => $this->data['mobile_code'].$this->data['mobile'],
            'gender' => $this->data['gender'] ?: null,
            'updated_at' => SallaMerchantService::parseDate(data: $this->data['updated_at']),
        ]);
    }
}
