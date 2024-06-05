<?php

namespace App\Jobs\Zid\Installation;

use App\Jobs\Concerns\InteractsWithException;
use App\Services\Zid\Merchant\ZidMerchantException;
use App\Services\Zid\Merchant\ZidMerchantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RegisterWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $managerToken,
        public string $accessToken,
        public int $providerId,
        public string $event,
    ) {
        $this->maxAttempts = 5;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new ZidMerchantService(
            managerToken: $this->managerToken,
            accessToken: $this->accessToken,
        );

        try {
            $service->webhooks()->register(
                providerId: $this->providerId,
                event: $this->event,
            );
        } catch (ZidMerchantException $e) {
            $this->handleException(
                e: new ZidMerchantException(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Exception while registering zid webhook',
                            "ProviderId: {$this->providerId}",
                            "Event: {$this->event}",
                            "Reason: {$e->getMessage()}",
                        ],
                    ),
                    code: $e->getCode(),
                ),
            );

            return;
        }
    }
}
