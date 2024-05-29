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
                            'Error while handling salla customer otp request webhook',
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
        if ($whatsappAccount->is_expired || $whatsappAccount->is_sending_disabled) {
            return;
        }

        $template = $store->templates()->key(key: MessageTemplate::SALLA_OTP)->first();
        if ($template->is_disabled) {
            return;
        }

        $mobile = $this->data['contact'];
        if (isInBlacklistedMobiles(mobile: $mobile, store: $store)) {
            return;
        }

        $message = str(string: $template->message)
            ->replace(search: '{OTP}', replace: $this->data['code'])
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
