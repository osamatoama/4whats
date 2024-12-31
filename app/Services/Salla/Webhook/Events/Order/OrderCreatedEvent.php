<?php

namespace App\Services\Salla\Webhook\Events\Order;

use App\Enums\Jobs\QueueName;
use App\Jobs\Salla\Webhook\Order\SallaOrderCreatedJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class OrderCreatedEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaOrderCreatedJob::dispatch(
            event: $event,
            merchantId: $merchantId,
            data: $data,
        )->onQueue(
            queue: QueueName::ORDERS->value
        );
    }
}
