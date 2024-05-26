<?php

namespace App\Services\Salla\Webhook\Events\App;

use App\Jobs\Salla\Webhook\App\SallaAppUninstalledJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class AppUninstalledEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaAppUninstalledJob::dispatch(
            merchantId: $merchantId,
            data: $data,
        );
    }
}
