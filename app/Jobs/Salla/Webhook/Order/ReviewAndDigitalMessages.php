<?php

namespace App\Jobs\Salla\Webhook\Order;

use App\Enums\MessageTemplate;
use App\Enums\SettingKey;
use App\Jobs\Whatsapp\WhatsappSendTextMessageJob;
use App\Models\OrderStatus;
use App\Models\Store;

trait ReviewAndDigitalMessages
{
    protected function sendReviewMessage(Store $store, OrderStatus $orderStatus): void
    {
        if ($this->data['rating_link'] === null) {
            return;
        }

        $template = $store->templates()->key(key: MessageTemplate::SALLA_REVIEW_ORDER)->first();
        if ($template->is_disabled) {
            return;
        }

        $reviewStatusId = settings(storeId: $store->id, eager: false)->value(key: SettingKey::STORE_ORDER_STATUS_ID_FOR_REVIEW_ORDER_MESSAGE);
        if ($reviewStatusId != $orderStatus->id) {
            return;
        }

        $mobile = $this->data['customer']['mobile_code'].$this->data['customer']['mobile'];
        $message = str(string: $template->message)
            ->replace(search: '{REVIEW_URL}', replace: $this->data['rating_link'])
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

    protected function sendDigitalMessage(Store $store): void
    {
        $template = $store->templates()->key(key: MessageTemplate::SALLA_DIGITAL_PRODUCT)->first();
        if ($template->is_disabled) {
            return;
        }

        $products = str(string: '');
        foreach ($this->data['items'] as $item) {
            $type = $item['product']['type'];
            if ($type !== 'digital' && $type !== 'codes') {
                continue;
            }

            if (empty($item['files']) && empty($item['codes'])) {
                continue;
            }

            $products = $products->append(values: "*{$item['name']}*")->newLine();

            if ($type === 'digital') {
                foreach ($item['files'] as $file) {
                    $products = $products->append(values: $file['name'])->newLine()->append(values: $file['url'])->newLine();
                }
            }

            if ($type === 'codes') {
                foreach ($item['codes'] as $code) {
                    $products = $products->append(values: $code['code'])->newLine();
                }
            }

            $products = $products->newLine();
        }
        $products = $products->trim(characters: PHP_EOL);

        if ($products->isEmpty()) {
            return;
        }

        $mobile = $this->data['customer']['mobile_code'].$this->data['customer']['mobile'];
        $message = str(string: $template->message)
            ->replace(search: '{CUSTOMER_NAME}', replace: $this->data['customer']['first_name'].' '.$this->data['customer']['last_name'])
            ->replace(search: '{ORDER_ID}', replace: $this->data['reference_id'])
            ->replace(search: '{PRODUCTS}', replace: $products->toString())
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
