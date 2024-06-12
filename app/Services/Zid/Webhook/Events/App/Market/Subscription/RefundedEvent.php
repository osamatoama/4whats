<?php

namespace App\Services\Zid\Webhook\Events\App\Market\Subscription;

use App\Jobs\Zid\Webhook\App\Market\Subscription\RefundedJob;
use App\Services\Zid\Webhook\Contracts\WebhookEvent;

class RefundedEvent implements WebhookEvent
{
    public function __invoke(string $event, int $providerId, array $data): void
    {
        RefundedJob::dispatch(
            event: $event,
            providerId: $providerId,
            data: $data,
        );
    }
}
