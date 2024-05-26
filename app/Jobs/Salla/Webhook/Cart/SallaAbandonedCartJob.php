<?php

namespace App\Jobs\Salla\Webhook\Cart;

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

class SallaAbandonedCartJob implements ShouldQueue
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
                    message: "Error while handling salla abandoned cart webhook | Merchant: {$this->merchantId} | Message: Store not found",
                ),
                fail: true,
            );

            return;
        }

        $store->abandonedCarts()->updateOrCreate(attributes: [
            'provider_type' => ProviderType::SALLA,
            'provider_id' => $this->data['id'],
        ], values: [
            'contact_id' => $this->getContactId(store: $store),
            'total_amount' => $this->data['total']['amount'] * 100,
            'total_currency' => $this->data['total']['currency'],
            'checkout_url' => $this->data['checkout_url'],
            'created_at' => SallaMerchantService::parseDate(data: $this->data['created_at']),
            'updated_at' => SallaMerchantService::parseDate(data: $this->data['updated_at']),
        ]);

        if ($store->is_expired) {
            return;
        }

        $template = $store->templates()->key(key: MessageTemplate::SALLA_ABANDONED_CART)->first();
        if ($template->is_disabled) {
            return;
        }

        $mobile = $this->data['mobile_code'].$this->data['mobile'];
        $message = str(string: $template->message)
            ->replace(search: '{CUSTOMER_NAME}', replace: $this->data['customer']['name'])
            ->replace(search: '{AMOUNT}', replace: $this->data['total']['amount'])
            ->replace(search: '{CURRENCY}', replace: $this->data['total']['currency'])
            ->replace(search: '{CHECKOUT_URL}', replace: $this->data['checkout_url'])
            ->toString();

        WhatsappSendTextMessageJob::dispatch(
            storeId: $store->id,
            instanceId: $store->whatsappAccount->instance_id,
            instanceToken: $store->whatsappAccount->instance_token,
            mobile: $mobile,
            message: $message,
        )->delay(delay: $template->delay_in_seconds);
    }

    protected function getContactId(Store $store): int
    {
        $name = str(string: $this->data['customer']['name']);

        return $store->contacts()->firstOrCreate(attributes: [
            'provider_type' => ProviderType::SALLA,
            'provider_id' => $this->data['customer']['id'],
            'source' => ContactSource::SALLA,
        ], values: [
            'first_name' => $name->before(search: ' ')->toString(),
            'last_name' => $name->after(search: ' ')->toString(),
            'email' => $this->data['customer']['email'],
            'phone' => $this->data['customer']['mobile'],
            'gender' => null,
        ])->id;
    }
}
