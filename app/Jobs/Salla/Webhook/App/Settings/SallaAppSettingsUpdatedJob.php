<?php

namespace App\Jobs\Salla\Webhook\App\Settings;

use App\Dto\WidgetDto;
use App\Jobs\Concerns\InteractsWithException;
use App\Models\Store;
use App\Services\Widget\WidgetService;
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
                            'Error while handling salla app settings updated webhook',
                            "Merchant: {$this->merchantId}",
                            'Reason: Store not found',
                        ],
                    ),
                    code: 404,
                ),
                delay: 10,
            );

            return;
        }

        $settings = $this->data['settings'];

        (new WidgetService())->update(
            widget: $store->widget,
            widgetDto: WidgetDto::fromSallaWebhook(
                storeId: $store->id,
                settings: $settings,
            ),
            updateSallaWidget: false,
        );
    }
}
