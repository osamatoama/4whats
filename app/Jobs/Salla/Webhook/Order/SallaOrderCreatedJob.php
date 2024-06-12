<?php

namespace App\Jobs\Salla\Webhook\Order;

use App\Enums\MessageTemplate;
use App\Enums\SettingKey;
use App\Jobs\Concerns\InteractsWithException;
use App\Jobs\Whatsapp\WhatsappSendTextMessageJob;
use App\Models\Store;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SallaOrderCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithException, InteractsWithQueue, Queueable, ReviewAndDigitalMessages, SerializesModels;

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
                            'Error while handling salla order created webhook',
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

        $whatsappAccount = $store->whatsappAccount;
        if ($store->is_uninstalled || $whatsappAccount->is_expired || $whatsappAccount->is_sending_disabled) {
            return;
        }

        $this->sendToEmployees(store: $store);

        $sallaOrderStatusId = $this->data['status']['customized']['id'];
        $orderStatus = $store->orderStatuses()->where(column: 'provider_id', operator: '=', value: $sallaOrderStatusId)->first();
        if ($orderStatus === null) {
            $this->handleException(
                e: new Exception(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Error while handling salla order created webhook',
                            "Store: {$store->id}",
                            "Status: {$sallaOrderStatusId}",
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
                            'Error while handling salla order created webhook',
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

        $mobile = $this->data['customer']['mobile_code'].$this->data['customer']['mobile'];
        if (isInBlacklistedMobiles(mobile: $mobile, store: $store)) {
            return;
        }

        if ($template->is_enabled) {
            $message = str(string: $template->message)
                ->replace(search: '{CUSTOMER_NAME}', replace: $this->data['customer']['first_name'].' '.$this->data['customer']['last_name'])
                ->replace(search: '{ORDER_ID}', replace: $this->data['reference_id'])
                ->replace(search: '{ORDER_URL}', replace: $this->data['urls']['customer'])
                ->replace(search: '{AMOUNT}', replace: $this->data['amounts']['total']['amount'])
                ->replace(search: '{STATUS}', replace: $this->data['status']['customized']['name'])
                ->replace(search: '{CURRENCY}', replace: $this->data['amounts']['total']['currency'])
                ->replace(search: '{SHIPPING_COMPANY}', replace: data_get(target: $this->data, key: 'shipments.0.courier_name', default: ''))
                ->replace(search: '{TRACKING_NUMBER}', replace: data_get(target: $this->data, key: 'shipments.0.tracking_number', default: ''))
                ->replace(search: '{TRACKING_URL}', replace: data_get(target: $this->data, key: 'shipments.0.tracking_link', default: ''))
                ->toString();

            WhatsappSendTextMessageJob::dispatch(
                storeId: $store->id,
                instanceId: $whatsappAccount->instance_id,
                instanceToken: $whatsappAccount->instance_token,
                mobile: $mobile,
                message: $message,
            )->delay(delay: $template->delay_in_seconds);
        }

        $this->sendReviewMessage(store: $store, orderStatus: $orderStatus);
        $this->sendCODMessage(store: $store);
        $this->sendDigitalMessage(store: $store);
    }

    protected function sendToEmployees(Store $store): void
    {
        $template = $store->templates()->key(key: MessageTemplate::SALLA_NEW_ORDER_FOR_EMPLOYEES)->first();
        if ($template->is_disabled) {
            return;
        }

        $message = str(string: $template->message)
            ->replace(search: '{CUSTOMER_NAME}', replace: $this->data['customer']['first_name'].' '.$this->data['customer']['last_name'])
            ->replace(search: '{ORDER_ID}', replace: $this->data['reference_id'])
            ->replace(search: '{ORDER_URL}', replace: $this->data['urls']['customer'])
            ->replace(search: '{AMOUNT}', replace: $this->data['amounts']['total']['amount'])
            ->replace(search: '{STATUS}', replace: $this->data['status']['customized']['name'])
            ->replace(search: '{CURRENCY}', replace: $this->data['amounts']['total']['currency'])
            ->replace(search: '{SHIPPING_COMPANY}', replace: data_get(target: $this->data, key: 'shipments.0.courier_name', default: ''))
            ->replace(search: '{TRACKING_NUMBER}', replace: data_get(target: $this->data, key: 'shipments.0.tracking_number', default: ''))
            ->replace(search: '{TRACKING_URL}', replace: data_get(target: $this->data, key: 'shipments.0.tracking_link', default: ''))
            ->toString();

        $mobiles = settings(storeId: $store->id, eager: false)->value(key: SettingKey::STORE_EMPLOYEES_MOBILES_FOR_NEW_ORDER_MESSAGE);
        $mobiles = explode(separator: ',', string: $mobiles);
        foreach ($mobiles as $mobile) {
            WhatsappSendTextMessageJob::dispatch(
                storeId: $store->id,
                instanceId: $store->whatsappAccount->instance_id,
                instanceToken: $store->whatsappAccount->instance_token,
                mobile: trim(string: $mobile),
                message: $message,
            )->delay(delay: $template->delay_in_seconds);
        }
    }

    protected function sendCODMessage(Store $store): void
    {
        if ($this->data['payment_method'] !== 'cod') {
            return;
        }

        $template = $store->templates()->key(key: MessageTemplate::SALLA_COD)->first();
        if ($template->is_disabled) {
            return;
        }

        $mobile = $this->data['customer']['mobile_code'].$this->data['customer']['mobile'];
        $message = str(string: $template->message)
            ->replace(search: '{CUSTOMER_NAME}', replace: $this->data['customer']['first_name'].' '.$this->data['customer']['last_name'])
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
