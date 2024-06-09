<?php

namespace App\Jobs\Salla\Push\Settings;

use App\Dto\WidgetDto;
use App\Jobs\Concerns\InteractsWithException;
use App\Services\Salla\Partner\SallaPartnerException;
use App\Services\Salla\Partner\SallaPartnerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SallaPushWidgetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $accessToken,
        public int $storeId,
        public WidgetDto $widgetDto,
    ) {
        $this->maxAttempts = 5;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $service = new SallaPartnerService(
            accessToken: $this->accessToken,
        );

        try {
            $service->settings()->update(
                data: [
                    'widget_mobile' => $this->widgetDto->mobile,
                    'widget_message' => $this->widgetDto->message,
                    'widget_color' => $this->widgetDto->color,
                    'widget_is_enabled' => $this->widgetDto->isEnabled,
                ],
            );
        } catch (SallaPartnerException $e) {
            $this->handleException(
                e: new SallaPartnerException(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Exception while pushing widget to salla',
                            "Store: {$this->storeId}",
                            "Reason: {$e->getMessage()}",
                        ],
                    ),
                    code: $e->getCode(),
                ),
            );
        }
    }
}
