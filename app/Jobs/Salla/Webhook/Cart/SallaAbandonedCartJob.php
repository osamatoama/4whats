<?php

namespace App\Jobs\Salla\Webhook\Cart;

use App\Dto\AbandonedCartDto;
use App\Dto\ContactDto;
use App\Enums\MessageTemplate;
use App\Jobs\Concerns\InteractsWithException;
use App\Jobs\Whatsapp\WhatsappSendTextMessageJob;
use App\Models\Store;
use App\Services\AbandonedCart\AbandonedCartService;
use App\Services\Contact\ContactService;
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
        $this->maxAttempts = 5;
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
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Error while handling salla abandoned cart webhook',
                            "Merchant: {$this->merchantId}",
                            'Reason: Store not found',
                        ],
                    ),
                    code: 404,
                ),
                fail: true,
            );

            return;
        }

        $contact = (new ContactService())->firstOrCreate(
            contactDto: ContactDto::fromSallaAbandonedCart(
                storeId: $store->id,
                data: $this->data,
            ),
        );

        (new AbandonedCartService())->updateOrCreate(
            abandonedCartDto: AbandonedCartDto::fromSalla(
                storeId: $store->id,
                contactId: $contact->id,
                data: $this->data,
            ),
        );

        $whatsappAccount = $store->whatsappAccount;
        if ($store->is_uninstalled || $whatsappAccount->is_expired || $whatsappAccount->is_sending_disabled) {
            return;
        }

        $template = $store->templates()->key(key: MessageTemplate::SALLA_ABANDONED_CART)->first();
        if ($template->is_disabled) {
            return;
        }

        $mobile = $this->data['customer']['mobile_code'].$this->data['customer']['mobile'];
        if (isInBlacklistedMobiles(mobile: $mobile, store: $store)) {
            return;
        }

        $message = str(string: $template->message)
            ->replace(search: '{CUSTOMER_NAME}', replace: $this->data['customer']['name'])
            ->replace(search: '{AMOUNT}', replace: $this->data['total']['amount'])
            ->replace(search: '{CURRENCY}', replace: $this->data['total']['currency'])
            ->replace(search: '{CHECKOUT_URL}', replace: $this->data['checkout_url'])
            ->toString();

        WhatsappSendTextMessageJob::dispatch(
            storeId: $store->id,
            instanceId: $whatsappAccount->instance_id,
            instanceToken: $whatsappAccount->instance_token,
            mobile: $mobile,
            message: $message,
        )->delay(delay: $template->delay_in_seconds);
    }
}
