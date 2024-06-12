<?php

namespace App\Services\Zid\Webhook\Events\App\Market\Subscription;

use App\Jobs\Zid\Webhook\App\Market\Subscription\RenewJob;
use App\Services\Zid\Webhook\Contracts\WebhookEvent;

class RenewEvent implements WebhookEvent
{
    public function __invoke(string $event, int $providerId, array $data): void
    {
        RenewJob::dispatch(
            event: $event,
            providerId: $providerId,
            data: $data,
        );
    }
}
