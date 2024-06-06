<?php

namespace App\Services\Zid\Webhook\Events\Order;

use App\Jobs\Zid\Webhook\Order\OrderStatusUpdateJob;
use App\Services\Zid\Webhook\Contracts\WebhookEvent;

class OrderStatusUpdateEvent implements WebhookEvent
{
    public function __invoke(string $event, int $providerId, array $data): void
    {
        OrderStatusUpdateJob::dispatch(
            event: $event,
            providerId: $providerId,
            data: $data,
        );
    }
}
