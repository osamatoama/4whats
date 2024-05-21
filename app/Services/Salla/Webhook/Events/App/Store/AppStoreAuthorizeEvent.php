<?php

namespace App\Services\Salla\Webhook\Events\App\Store;

use App\Jobs\Salla\Webhook\App\Store\SallaAppStoreAuthorizeJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class AppStoreAuthorizeEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaAppStoreAuthorizeJob::dispatch(
            merchantId: $merchantId,
            data: $data,
        );
    }
}
