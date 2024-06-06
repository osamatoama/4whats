<?php

namespace App\Jobs\Zid\Webhook\AbandonedCart;

use App\Dto\AbandonedCartDto;
use App\Dto\ContactDto;
use App\Enums\MessageTemplate;
use App\Jobs\Concerns\InteractsWithException;
use App\Jobs\Whatsapp\WhatsappSendTextMessageJob;
use App\Jobs\Zid\Contracts\WebhookJob;
use App\Models\Store;
use App\Services\AbandonedCart\AbandonedCartService;
use App\Services\Contact\ContactService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AbandonedCartCreatedJob implements ShouldQueue, WebhookJob
{
    use Dispatchable, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly string $event,
        public readonly int $providerId,
        public readonly array $data
    ) {
        $this->maxAttempts = 5;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $store = Store::query()->zid(providerId: $this->providerId)->first();
        if ($store === null) {
            $this->handleException(
                e: new Exception(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            "Error while handling zid {$this->event} webhook",
                            "ProviderID: {$this->providerId}",
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
            contactDto: ContactDto::fromZidAbandonedCart(
                storeId: $store->id,
                data: $this->data,
            ),
        );

        (new AbandonedCartService())->updateOrCreate(
            abandonedCartDto: AbandonedCartDto::fromZid(
                storeId: $store->id,
                contactId: $contact->id,
                data: $this->data,
            ),
        );

        $whatsappAccount = $store->whatsappAccount;
        if ($whatsappAccount->is_expired || $whatsappAccount->is_sending_disabled) {
            return;
        }

        $template = $store->templates()->key(key: MessageTemplate::ZID_ABANDONED_CART)->first();
        if ($template->is_disabled) {
            return;
        }

        $mobile = ensureMobileStartingWithPlus(mobile: $this->data['customer_mobile']);
        if (isInBlacklistedMobiles(mobile: $mobile, store: $store)) {
            return;
        }

        $currency = str(string: $this->data['cart_total_string'])->after(search: ' ')->toString();
        $message = str(string: $template->message)
            ->replace(search: '{CUSTOMER_NAME}', replace: $this->data['customer_name'])
            ->replace(search: '{AMOUNT}', replace: $this->data['cart_total'])
            ->replace(search: '{CURRENCY}', replace: $currency)
            ->replace(search: '{CHECKOUT_URL}', replace: $this->data['url'])
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
