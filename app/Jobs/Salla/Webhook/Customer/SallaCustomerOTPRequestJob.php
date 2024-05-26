<?php

namespace App\Jobs\Salla\Webhook\Customer;

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

class SallaCustomerOTPRequestJob implements ShouldQueue
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
                    message: "Error while handling salla customer otp request webhook | Merchant: {$this->merchantId} | Message: Store not found",
                ),
                fail: true,
            );

            return;
        }

        $template = $store->templates()->key(key: MessageTemplate::SALLA_OTP)->first();
        if ($template->is_disabled) {
            return;
        }

        $mobile = $this->data['contact'];
        $message = str(string: $template->message)
            ->replace(search: '{OTP}', replace: $this->data['code'])
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
