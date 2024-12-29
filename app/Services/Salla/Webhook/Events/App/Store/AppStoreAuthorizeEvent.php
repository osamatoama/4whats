<?php

namespace App\Services\Salla\Webhook\Events\App\Store;

use App\Enums\Jobs\QueueName;
use App\Jobs\Salla\Webhook\App\Store\SallaAppStoreAuthorizeJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class AppStoreAuthorizeEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaAppStoreAuthorizeJob::dispatch(
            event: $event,
            merchantId: $merchantId,
            data: $data,
        )->onQueue(
            queue: QueueName::SUBSCRIPTIONS->value
        );
    }
}
