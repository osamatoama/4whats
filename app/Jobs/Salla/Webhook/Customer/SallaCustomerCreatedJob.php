<?php

namespace App\Jobs\Salla\Webhook\Customer;

use App\Enums\ContactSource;
use App\Enums\MessageTemplate;
use App\Enums\ProviderType;
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
                    message: "Error while handling salla customer created webhook | Merchant: {$this->merchantId} | Message: Store not found",
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

        if ($store->is_expired) {
            return;
        }

        $template = $store->templates()->key(key: MessageTemplate::SALLA_CUSTOMER_CREATED)->first();
        if ($template->is_disabled) {
            return;
        }

        $mobile = $this->data['mobile_code'].$this->data['mobile'];
        if (isInBlacklistedMobiles(mobile: $mobile, store: $store)) {
            return;
        }

        $message = str(string: $template->message)
            ->replace(search: '{CUSTOMER_NAME}', replace: $this->data['first_name'].' '.$this->data['first_name'])
            ->toString();

        WhatsappSendTextMessageJob::dispatch(
            storeId: $store->id,
            instanceId: $store->whatsappAccount->instance_id,
            instanceToken: $store->whatsappAccount->instance_token,
            mobile: $mobile,
            message: $message,
        )->delay(delay: $template->delay_in_seconds);
    }
}
