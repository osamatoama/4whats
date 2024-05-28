<?php

namespace App\Jobs\Salla\Webhook\App\Settings;

use App\Jobs\Concerns\InteractsWithException;
use App\Models\Store;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SallaAppSettingsUpdatedJob implements ShouldQueue
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
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Error while handling salla app settings updated webhook',
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

        $settings = $this->data['settings'];

        $store->widget()->update(values: [
            'message' => $settings['widget_message'],
            'color' => $settings['widget_color'],
            'is_enabled' => $settings['widget_is_enabled'],
        ]);
    }
}
