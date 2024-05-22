<?php

namespace App\Jobs\Salla\Webhook\Order;

use App\Enums\Settings\StoreSettings;
use App\Enums\StoreMessageTemplate;
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

class SallaOrderCreatedJob implements ShouldQueue
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
                    message: "Error while handling salla order created webhook | Merchant: {$this->merchantId} | Message: Store not found",
                ),
                fail: true,
            );

            return;
        }

        $sallaOrderStatusId = $this->data['status']['customized']['id'];
        $orderStatus = $store->orderStatuses()->where(column: 'provider_id', operator: '=', value: $sallaOrderStatusId)->first();
        if ($orderStatus === null) {
            $this->handleException(
                e: new Exception(
                    message: "Error while handling salla order created webhook | Store: {$store->id} | Status: {$sallaOrderStatusId} | Message: Order status not found",
                ),
                fail: true,
            );

            return;
        }

        $messageTemplateKey = StoreMessageTemplate::generateOrderStatusKey(orderStatusId: $orderStatus->id);
        $messageTemplate = $store->messageTemplates()->key(key: $messageTemplateKey)->first();
        if ($messageTemplate === null) {
            $this->handleException(
                e: new Exception(
                    message: "Error while handling salla order created webhook | Store: {$store->id} | Key: {$messageTemplateKey} | Message: Message template not found",
                ),
                fail: true,
            );

            return;
        }

        if ($messageTemplate->is_enabled) {
            $mobile = $this->data['customer']['mobile_code'].$this->data['customer']['mobile'];
            $message = str(string: $messageTemplate->message)
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
            )->delay(delay: $messageTemplate->delay_in_seconds);
        }

        $this->sendReviewMessage(store: $store, orderStatus: $orderStatus);
        $this->sendCODMessage(store: $store);
        $this->sendToEmployees(store: $store);
    }

    protected function sendReviewMessage(Store $store, OrderStatus $orderStatus): void
    {
        $messageTemplate = $store->messageTemplates()->key(key: StoreMessageTemplate::SALLA_REVIEW_ORDER)->first();
        if ($messageTemplate->is_disabled) {
            return;
        }

        $reviewStatusId = settings(storeId: $store->id, eager: false)->value(key: StoreSettings::SALLA_CUSTOM_REVIEW_ORDER);
        if ($reviewStatusId != $orderStatus->id) {
            return;
        }

        $mobile = $this->data['customer']['mobile_code'].$this->data['customer']['mobile'];
        $message = str(string: $messageTemplate->message)
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
        )->delay(delay: $messageTemplate->delay_in_seconds);
    }

    protected function sendCODMessage(Store $store): void
    {
        if ($this->data['payment_method'] !== 'cod') {
            return;
        }

        $messageTemplate = $store->messageTemplates()->key(key: StoreMessageTemplate::SALLA_COD)->first();
        if ($messageTemplate->is_disabled) {
            return;
        }

        $mobile = $this->data['customer']['mobile_code'].$this->data['customer']['mobile'];
        $message = str(string: $messageTemplate->message)
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
        )->delay(delay: $messageTemplate->delay_in_seconds);
    }

    protected function sendToEmployees(Store $store): void
    {
        $messageTemplate = $store->messageTemplates()->key(key: StoreMessageTemplate::SALLA_NEW_ORDER_FOR_EMPLOYEES)->first();
        if ($messageTemplate->is_disabled) {
            return;
        }

        $mobiles = settings(storeId: $store->id, eager: false)->value(key: StoreSettings::SALLA_CUSTOM_NEW_ORDER_FOR_EMPLOYEES);
        $mobiles = explode(separator: ',', string: $mobiles);

        foreach ($mobiles as $mobile) {
            $mobile = trim(string: $mobile);
            $message = str(string: $messageTemplate->message)
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
            )->delay(delay: $messageTemplate->delay_in_seconds);
        }
    }
}
