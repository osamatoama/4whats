<?php

namespace App\Services\Zid\Webhook\Events\App\Market\Application;

use App\Jobs\Zid\Webhook\App\Market\Application\UninstallJob;
use App\Services\Zid\Webhook\Contracts\WebhookEvent;

class UninstallEvent implements WebhookEvent
{
    public function __invoke(string $event, int $providerId, array $data): void
    {
        UninstallJob::dispatch(
            event: $event,
            providerId: $providerId,
            data: $data,
        );
    }
}
