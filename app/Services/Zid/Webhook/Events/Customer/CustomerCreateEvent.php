<?php

namespace App\Services\Zid\Webhook\Events\Customer;

use App\Jobs\Zid\Webhook\Customer\CustomerCreateJob;
use App\Services\Zid\Webhook\Contracts\WebhookEvent;

class CustomerCreateEvent implements WebhookEvent
{
    public function __invoke(string $event, int $providerId, array $data): void
    {
        CustomerCreateJob::dispatch(
            event: $event,
            providerId: $providerId,
            data: $data,
        );
    }
}
