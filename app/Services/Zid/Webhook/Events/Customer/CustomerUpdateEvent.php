<?php

namespace App\Services\Zid\Webhook\Events\Customer;

use App\Jobs\Zid\Webhook\Customer\CustomerUpdateJob;
use App\Services\Zid\Webhook\Contracts\WebhookEvent;

class CustomerUpdateEvent implements WebhookEvent
{
    public function __invoke(string $event, int $providerId, array $data): void
    {
        CustomerUpdateJob::dispatch(
            event: $event,
            providerId: $providerId,
            data: $data,
        );
    }
}
