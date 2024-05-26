<?php

namespace App\Jobs\Salla\Webhook\Order;

use App\Enums\MessageTemplate;
use App\Enums\Settings\StoreSettings;
use App\Jobs\Concerns\InteractsWithException;
use App\Jobs\Whatsapp\WhatsappSendTextMessageJob;
use App\Models\OrderStatus;
use App\Models\Store;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SallaOrderUpdatedJob implements ShouldQueue
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
                    message: "Error while handling salla order updated webhook | Merchant: {$this->merchantId} | Message: Store not found",
                ),
                fail: true,
            );

            return;
        }

        if ($store->is_expired) {
            return;
        }

        $sallaOrderStatusId = $this->data['status']['customized']['id'];
        $orderStatus = $store->orderStatuses()->where(column: 'provider_id', operator: '=', value: $sallaOrderStatusId)->first();
        if ($orderStatus === null) {
            $this->handleException(
                e: new Exception(
                    message: "Error while handling salla order updated webhook | Store: {$store->id} | Status: {$sallaOrderStatusId} | Message: Order status not found",
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
                    message: "Error while handling salla order updated webhook | Store: {$store->id} | Key: {$templateKey} | Message: Message template not found",
                ),
                fail: true,
            );

            return;
        }

        if ($template->is_enabled) {
            $mobile = $this->data['customer']['mobile_code'].$this->data['customer']['mobile'];
            $message = str(string: $template->message)
                ->replace(search: '{CUSTOMER_NAME}', replace: $this->data['customer']['first_name'].' '.$this->data['customer']['first_name'])
                ->replace(search: '{ORDER_ID}', replace: $this->data['reference_id'])
                ->replace(search: '{STATUS}', replace: $this->data['status']['customized']['name'])
                ->toString();

            WhatsappSendTextMessageJob::dispatch(
                storeId: $store->id,
                instanceId: $store->whatsappAccount->instance_id,
                instanceToken: $store->whatsappAccount->instance_token,
                mobile: $mobile,
                message: $message,
            )->delay(delay: $template->delay_in_seconds);
        }

        $this->sendReviewMessage(store: $store, orderStatus: $orderStatus);
    }

    protected function sendReviewMessage(Store $store, OrderStatus $orderStatus): void
    {
        $template = $store->templates()->key(key: MessageTemplate::SALLA_REVIEW_ORDER)->first();
        if ($template->is_disabled) {
            return;
        }

        $reviewStatusId = settings(storeId: $store->id, eager: false)->value(key: StoreSettings::SALLA_CUSTOM_REVIEW_ORDER);
        if ($reviewStatusId != $orderStatus->id) {
            return;
        }

        $mobile = $this->data['customer']['mobile_code'].$this->data['customer']['mobile'];
        $message = str(string: $template->message)
            ->replace(search: '{REVIEW_URL}', replace: $this->data['rating_link'] ?? $this->data['urls']['customer'])
            ->replace(search: '{CUSTOMER_NAME}', replace: $this->data['customer']['first_name'].' '.$this->data['customer']['first_name'])
            ->replace(search: '{ORDER_ID}', replace: $this->data['reference_id'])
            ->replace(search: '{AMOUNT}', replace: $this->data['amounts']['total']['amount'])
            ->replace(search: '{STATUS}', replace: $this->data['status']['customized']['name'])
            ->replace(search: '{CURRENCY}', replace: $this->data['amounts']['total']['currency'])
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
