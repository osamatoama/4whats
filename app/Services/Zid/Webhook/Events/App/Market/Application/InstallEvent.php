<?php

namespace App\Services\Zid\Webhook\Events\App\Market\Application;

use App\Enums\Jobs\QueueName;
use App\Jobs\Zid\Webhook\App\Market\Application\InstallJob;
use App\Services\Zid\Webhook\Contracts\WebhookEvent;

class InstallEvent implements WebhookEvent
{
    public function __invoke(string $event, int $providerId, array $data): void
    {
        InstallJob::dispatch(
            event: $event,
            providerId: $providerId,
            data: $data,
        )->delay(now()->addMinutes(5))
        ->onQueue(QueueName::SUBSCRIPTIONS->value);
    }
}
