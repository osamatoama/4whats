<?php

namespace App\Services\Salla\Webhook\Events\Cart;

use App\Enums\Jobs\QueueName;
use App\Jobs\Salla\Webhook\Cart\SallaAbandonedCartJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class AbandonedCartEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaAbandonedCartJob::dispatch(
            event: $event,
            merchantId: $merchantId,
            data: $data,
        )->onQueue(
            queue: QueueName::ABANDONED_CARTS->value
        );
    }
}
