<?php

namespace App\Services\Salla\Webhook\Events\Customer;

use App\Enums\Jobs\QueueName;
use App\Jobs\Salla\Webhook\Customer\SallaCustomerCreatedJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class CustomerCreatedEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaCustomerCreatedJob::dispatch(
            event: $event,
            merchantId: $merchantId,
            data: $data,
        )->onQueue(
            queue: QueueName::CUSTOMERS->value
        );
    }
}
