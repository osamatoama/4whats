<?php

namespace App\Services\Salla\Webhook\Events\App\Subscription;

use App\Jobs\Salla\Webhook\App\Subscription\SallaAppSubscriptionRenewedJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class AppSubscriptionRenewedEvent implements SallaWebhookEvent
{
    public function handle(int $merchantId, array $data): void
    {
        SallaAppSubscriptionRenewedJob::dispatch(
            merchantId: $merchantId,
            data: $data,
        );
    }
}
