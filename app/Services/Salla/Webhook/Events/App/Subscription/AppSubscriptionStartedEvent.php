<?php

namespace App\Services\Salla\Webhook\Events\App\Subscription;

use App\Jobs\Salla\Webhook\App\Subscription\SallaAppSubscriptionStartedJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class AppSubscriptionStartedEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaAppSubscriptionStartedJob::dispatch(
            merchantId: $merchantId,
            data: $data,
        );
    }
}
