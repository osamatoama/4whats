<?php

namespace App\Services\Zid\Webhook\Events\Order;

use App\Jobs\Zid\Webhook\Order\OrderCreateJob;
use App\Services\Zid\Webhook\Contracts\WebhookEvent;

class OrderCreateEvent implements WebhookEvent
{
    public function __invoke(string $event, int $providerId, array $data): void
    {
        OrderCreateJob::dispatch(
            event: $event,
            providerId: $providerId,
            data: $data,
        );
    }
}
