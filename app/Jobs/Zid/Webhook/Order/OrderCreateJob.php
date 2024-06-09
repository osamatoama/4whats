<?php

namespace App\Jobs\Zid\Webhook\Order;

use App\Enums\MessageTemplate;
use App\Enums\SettingKey;
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

class OrderCreateJob implements ShouldQueue, WebhookJob
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

        $whatsappAccount = $store->whatsappAccount;
        if ($whatsappAccount->is_expired || $whatsappAccount->is_sending_disabled) {
            return;
        }

        $amount = round(num: $this->data['order_total'], precision: 2);
        $this->sendToEmployees(store: $store, amount: $amount);

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
                ->replace(search: '{AMOUNT}', replace: $amount)
                ->replace(search: '{STATUS}', replace: $this->data['order_status']['name'])
                ->replace(search: '{CURRENCY}', replace: $this->data['currency_code'])
                ->toString();

            WhatsappSendTextMessageJob::dispatch(
                storeId: $store->id,
                instanceId: $whatsappAccount->instance_id,
                instanceToken: $whatsappAccount->instance_token,
                mobile: $mobile,
                message: $message,
            )->delay(delay: $template->delay_in_seconds);
        }

        $this->sendCODMessage(store: $store, mobile: $mobile, amount: $amount);
        $this->sendDigitalMessage(store: $store, mobile: $mobile);
    }

    protected function sendToEmployees(Store $store, float $amount): void
    {
        $template = $store->templates()->key(key: MessageTemplate::ZID_NEW_ORDER_FOR_EMPLOYEES)->first();
        if ($template->is_disabled) {
            return;
        }

        $message = str(string: $template->message)
            ->replace(search: '{CUSTOMER_NAME}', replace: $this->data['customer']['name'])
            ->replace(search: '{ORDER_ID}', replace: $this->data['id'])
            ->replace(search: '{ORDER_URL}', replace: $this->data['order_url'])
            ->replace(search: '{AMOUNT}', replace: $amount)
            ->replace(search: '{STATUS}', replace: $this->data['order_status']['name'])
            ->replace(search: '{CURRENCY}', replace: $this->data['currency_code'])
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

    protected function sendCODMessage(Store $store, string $mobile, float $amount): void
    {
        if ($this->data['payment']['method']['code'] !== 'zid_cod') {
            return;
        }

        $template = $store->templates()->key(key: MessageTemplate::ZID_COD)->first();
        if ($template->is_disabled) {
            return;
        }

        $message = str(string: $template->message)
            ->replace(search: '{CUSTOMER_NAME}', replace: $this->data['customer']['name'])
            ->replace(search: '{ORDER_ID}', replace: $this->data['id'])
            ->replace(search: '{AMOUNT}', replace: $amount)
            ->replace(search: '{STATUS}', replace: $this->data['order_status']['name'])
            ->replace(search: '{CURRENCY}', replace: $this->data['currency_code'])
            ->toString();

        WhatsappSendTextMessageJob::dispatch(
            storeId: $store->id,
            instanceId: $store->whatsappAccount->instance_id,
            instanceToken: $store->whatsappAccount->instance_token,
            mobile: $mobile,
            message: $message,
        )->delay(delay: $template->delay_in_seconds);
    }

    protected function sendDigitalMessage(Store $store, string $mobile): void
    {
        $template = $store->templates()->key(key: MessageTemplate::ZID_DIGITAL_PRODUCT)->first();
        if ($template->is_disabled) {
            return;
        }

        $products = str(string: '');
        foreach ($this->data['products'] as $product) {
            if ($product['product_class'] !== 'voucher' || empty($product['vouchers'])) {
                continue;
            }

            $products = $products->append(values: "*{$product['name']}*")->newLine();

            foreach ($product['vouchers'] as $voucher) {
                $products = $products->append(values: $voucher['key'])->newLine();

                if ($voucher['serial_number'] !== null) {
                    $products = $products->append(values: $voucher['serial_number'])->newLine();
                }

                if ($voucher['pin_code'] !== null) {
                    $products = $products->append(values: $voucher['pin_code'])->newLine();
                }
            }

            $products = $products->newLine();
        }
        $products = $products->trim(characters: PHP_EOL);

        if ($products->isEmpty()) {
            return;
        }

        $message = str(string: $template->message)
            ->replace(search: '{CUSTOMER_NAME}', replace: $this->data['customer']['name'])
            ->replace(search: '{ORDER_ID}', replace: $this->data['id'])
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
