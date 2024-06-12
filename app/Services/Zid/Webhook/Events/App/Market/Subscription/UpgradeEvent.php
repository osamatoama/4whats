<?php

namespace App\Services\Zid\Webhook\Events\App\Market\Subscription;

use App\Jobs\Zid\Webhook\App\Market\Subscription\UpgradeJob;
use App\Services\Zid\Webhook\Contracts\WebhookEvent;

class UpgradeEvent implements WebhookEvent
{
    public function __invoke(string $event, int $providerId, array $data): void
    {
        UpgradeJob::dispatch(
            event: $event,
            providerId: $providerId,
            data: $data,
        );
    }
}
