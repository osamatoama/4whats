<?php

namespace App\Jobs\Salla\Webhook\Customer;

use App\Enums\ContactSource;
use App\Enums\ProviderType;
use App\Enums\StoreMessageTemplate;
use App\Jobs\Concerns\InteractsWithException;
use App\Jobs\Whatsapp\WhatsappSendTextMessageJob;
use App\Models\Store;
use App\Services\Salla\Merchant\SallaMerchantService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SallaCustomerCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
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
                    message: "Error while handling salla customer created webhook | Message: Store not found | Merchant: {$this->merchantId}",
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

        $messageTemplate = $store->messageTemplates()->key(key: StoreMessageTemplate::SALLA_CUSTOMER_CREATED)->first();
        if ($messageTemplate->is_disabled) {
            return;
        }

        $mobile = $this->data['mobile_code'].$this->data['mobile'];
        $message = str(string: $messageTemplate->message)
            ->replace(search: '{CUSTOMER_NAME}', replace: $this->data['first_name'].' '.$this->data['first_name'])
            ->toString();

        WhatsappSendTextMessageJob::dispatch(
            storeId: $store->id,
            instanceId: $store->whatsappAccount->instance_id,
            instanceToken: $store->whatsappAccount->instance_token,
            mobile: $mobile,
            message: $message,
        )->delay(delay: $messageTemplate->delay_in_seconds);
    }
}
