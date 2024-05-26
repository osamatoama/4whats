<?php

namespace App\Services\Salla\Webhook\Events\Order;

use App\Jobs\Salla\Webhook\Order\SallaOrderUpdatedJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class OrderUpdatedEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaOrderUpdatedJob::dispatch(
            event: $event,
            merchantId: $merchantId,
            data: $data,
        );
    }
}
