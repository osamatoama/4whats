<?php

namespace App\Services\Salla\Webhook\Events\App;

use App\Enums\Jobs\QueueName;
use App\Jobs\Salla\Webhook\App\SallaAppUninstalledJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class AppUninstalledEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaAppUninstalledJob::dispatch(
            event: $event,
            merchantId: $merchantId,
            data: $data,
        )->onQueue(
            queue: QueueName::SUBSCRIPTIONS->value
        );
    }
}
