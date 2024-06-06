<?php

namespace App\Services\Zid\Webhook\Events\AbandonedCart;

use App\Jobs\Zid\Webhook\AbandonedCart\AbandonedCartCreatedJob;
use App\Services\Zid\Webhook\Contracts\WebhookEvent;

class AbandonedCartCreatedEvent implements WebhookEvent
{
    public function __invoke(string $event, int $providerId, array $data): void
    {
        AbandonedCartCreatedJob::dispatch(
            event: $event,
            providerId: $providerId,
            data: $data,
        );
    }
}
