<?php

namespace App\Jobs\Salla\Push\Settings;

use App\Jobs\Concerns\InteractsWithException;
use App\Services\Salla\Partner\Dto\SettingsDto;
use App\Services\Salla\Partner\SallaPartnerException;
use App\Services\Salla\Partner\SallaPartnerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SallaPushSettingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $accessToken,
        public int $storeId,
        public SettingsDto $settingsDto,
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
                settingsDto: $this->settingsDto,
            );
        } catch (SallaPartnerException $e) {
            $this->handleException(
                e: new SallaPartnerException(
                    message: generateMessageUsingSeparatedLines(
                        lines: [
                            'Exception while pushing settings to salla',
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
