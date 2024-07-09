<?php

namespace App\Services\Salla\Webhook\Events\App\Trial;

use App\Jobs\Salla\Webhook\App\Trial\SallaAppTrialCanceledJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class AppTrialCanceledEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaAppTrialCanceledJob::dispatch(
            event: $event,
            merchantId: $merchantId,
            data: $data,
        );
    }
}
