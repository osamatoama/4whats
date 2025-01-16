<?php

namespace App\Jobs\FourWhats;

use App\Jobs\Concerns\InteractsWithException;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FourWhatsSetInstanceWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $instanceId,
        public string $instanceToken,
    ) {
        $this->maxAttempts = 10;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new FourWhatsService();
        try {
            $service->webhook(
                instanceId: $this->instanceId,
                instanceToken: $this->instanceToken
            )->set(
                url: route(name: 'api.v1.webhooks.four-whats'),
            );
        } catch (FourWhatsException $e) {
//            $this->handleException(
//                e: new FourWhatsException(
//                    message: generateMessageUsingSeparatedLines(
//                        lines: [
//                            'Exception while setting four whats instance webhook',
//                            "Id: {$this->instanceId}",
//                            "Token: {$this->instanceToken}",
//                            "Reason: {$e->getMessage()}",
//                        ],
//                    ),
//                    code: $e->getCode(),
//                ),
//                fail: true,
//            );

            logger()->error(
                message: 'Exception while setting four whats instance webhook',
                context: [
                    'id'          => "Id: {$this->instanceId}",
                    'token'       => "Token: {$this->instanceToken}",
                    'reason'      => "Reason: {$e->getMessage()}",
                    'retry_after' => "Retry after: " . 60 * $this->attempts() . " seconds",
                ]
            );

            $this->release(
                delay: 60 * $this->attempts(),
            );

            return;
        }
    }
}
