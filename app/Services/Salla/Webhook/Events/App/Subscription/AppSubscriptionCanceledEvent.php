<?php

namespace App\Services\Salla\Webhook\Events\App\Subscription;

use App\Enums\Jobs\QueueName;
use App\Jobs\Salla\Webhook\App\Subscription\SallaAppSubscriptionCanceledJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class AppSubscriptionCanceledEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaAppSubscriptionCanceledJob::dispatch(
            event: $event,
            merchantId: $merchantId,
            data: $data,
        )->onQueue(
            queue: QueueName::SUBSCRIPTIONS->value
        );
    }
}
