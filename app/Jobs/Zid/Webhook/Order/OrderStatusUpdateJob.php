<?php

namespace App\Jobs\Zid\Webhook\Order;

use App\Enums\MessageTemplate;
use App\Jobs\Concerns\InteractsWithException;
use App\Jobs\Whatsapp\WhatsappSendTextMessageJob;
use App\Jobs\Zid\Contracts\WebhookJob;
use App\Models\Store;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdateJob implements ShouldQueue, WebhookJob
{
    use Dispatchable, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly string $event,
        public readonly int $providerId,
        public readonly array $data,
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

        $whatsappAccount = $store->whatsappAccount;
        if ($store->is_uninstalled || $whatsappAccount->is_expired || $whatsappAccount->is_sending_disabled) {
            return;
        }

        $sallaOrderStatusProviderId = $this->data['order_status']['code'];
        $orderStatus = $store->orderStatuses()->where(column: 'provider_id', operator: '=', value: $sallaOrderStatusProviderId)->first();
        if ($orderStatus === null) {
            $this->handleException(
                e: new Exception(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            "Error while handling zid {$this->event} webhook",
                            "Store: {$store->id}",
                            "Status: {$sallaOrderStatusProviderId}",
                            'Reason: Order status not found',
                        ],
                    ),
                    code: 404,
                ),
                fail: true,
            );

            return;
        }

        $templateKey = MessageTemplate::generateOrderStatusKey(orderStatusId: $orderStatus->id);
        $template = $store->templates()->key(key: $templateKey)->first();
        if ($template === null) {
            $this->handleException(
                e: new Exception(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            "Error while handling zid {$this->event} webhook",
                            "Store: {$store->id}",
                            "Template: {$templateKey}",
                            'Reason: Template not found',
                        ],
                    ),
                    code: 404,
                ),
                fail: true,
            );

            return;
        }

        $mobile = ensureMobileStartingWithPlus(mobile: $this->data['customer']['mobile']);
        if (isInBlacklistedMobiles(mobile: $mobile, store: $store)) {
            return;
        }

        if ($template->is_enabled) {
            $message = str(string: $template->message)
                ->replace(search: '{CUSTOMER_NAME}', replace: $this->data['customer']['name'])
                ->replace(search: '{ORDER_ID}', replace: $this->data['id'])
                ->replace(search: '{ORDER_URL}', replace: $this->data['order_url'])
                ->replace(search: '{AMOUNT}', replace: $this->data['order_total'])
                ->replace(search: '{STATUS}', replace: $this->data['order_status']['name'])
                ->replace(search: '{CURRENCY}', replace: $this->data['currency_code'])
                ->replace(search: '{SHIPPING_COMPANY}', replace: $this->data['shipping']['method']['name'])
                ->replace(search: '{TRACKING_NUMBER}', replace: $this->data['shipping']['method']['tracking']['number'])
                ->replace(search: '{TRACKING_URL}', replace: $this->data['shipping']['method']['tracking']['url'])
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
}
