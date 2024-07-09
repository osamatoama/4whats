<?php

namespace App\Services\Salla\Webhook\Events\App\Trial;

use App\Jobs\Salla\Webhook\App\Trial\SallaAppTrialStartedJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class AppTrialStartedEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaAppTrialStartedJob::dispatch(
            event: $event,
            merchantId: $merchantId,
            data: $data,
        );
    }
}
