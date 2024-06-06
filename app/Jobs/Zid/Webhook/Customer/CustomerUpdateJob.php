<?php

namespace App\Jobs\Zid\Webhook\Customer;

use App\Dto\ContactDto;
use App\Jobs\Concerns\InteractsWithException;
use App\Jobs\Zid\Contracts\WebhookJob;
use App\Models\Store;
use App\Services\Contact\ContactService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CustomerUpdateJob implements ShouldQueue, WebhookJob
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

        (new ContactService())->updateOrCreate(
            contactDto: ContactDto::fromZidWebhook(
                storeId: $store->id,
                data: $this->data,
            ),
        );
    }
}
