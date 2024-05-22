<?php

namespace App\Jobs\Salla\Webhook\Cart;

use App\Enums\MessageTemplate;
use App\Jobs\Concerns\InteractsWithException;
use App\Jobs\Whatsapp\WhatsappSendTextMessageJob;
use App\Models\Store;
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

        $messageTemplate = $store->messageTemplates()->key(key: MessageTemplate::SALLA_ABANDONED_CART)->first();
        if ($messageTemplate->is_disabled) {
            return;
        }

        $mobile = $this->data['mobile_code'].$this->data['mobile'];
        $message = str(string: $messageTemplate->message)
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
        )->delay(delay: $messageTemplate->delay_in_seconds);
    }
}
