<?php

namespace App\Services\Zid\Webhook\Events\AbandonedCart;

use App\Jobs\Zid\Webhook\AbandonedCart\AbandonedCartCompletedJob;
use App\Services\Zid\Webhook\Contracts\WebhookEvent;

class AbandonedCartCompletedEvent implements WebhookEvent
{
    public function __invoke(string $event, int $providerId, array $data): void
    {
        AbandonedCartCompletedJob::dispatch(
            event: $event,
            providerId: $providerId,
            data: $data,
        );
    }
}
