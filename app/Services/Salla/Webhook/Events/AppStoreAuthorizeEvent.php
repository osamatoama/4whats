<?php

namespace App\Services\Salla\Webhook\Events;

use App\Jobs\Salla\Webhook\SallaAppStoreAuthorizeJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class AppStoreAuthorizeEvent implements SallaWebhookEvent
{
    public function handle(int $merchantId, array $data): void
    {
        SallaAppStoreAuthorizeJob::dispatch(
            merchantId: $merchantId,
            data: $data,
        );
    }
}
