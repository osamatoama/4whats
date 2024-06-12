<?php

namespace App\Jobs\Zid\Webhook\App\Market\Subscription;

use App\Jobs\Zid\Contracts\WebhookJob;
use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RefundedJob implements ShouldQueue, WebhookJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly string $event,
        public readonly int $providerId,
        public readonly array $data,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $store = Store::query()->zid(providerId: $this->providerId)->first();
        if ($store === null) {
            return;
        }

        DB::transaction(
            callback: function () use ($store) {
                $store->update(
                    attributes: [
                        'is_uninstalled' => true,
                    ],
                );

                $store->user()->update(
                    values: [
                        'is_uninstalled' => true,
                    ],
                );
            },
        );
    }
}
