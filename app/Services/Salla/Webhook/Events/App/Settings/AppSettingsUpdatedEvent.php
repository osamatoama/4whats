<?php

namespace App\Services\Salla\Webhook\Events\App\Settings;

use App\Jobs\Salla\Webhook\App\Settings\SallaAppSettingsUpdatedJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class AppSettingsUpdatedEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaAppSettingsUpdatedJob::dispatch(
            event: $event,
            merchantId: $merchantId,
            data: $data,
        );
    }
}
