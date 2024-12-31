<?php

namespace App\Services\Salla\Webhook\Events\Customer;

use App\Enums\Jobs\QueueName;
use App\Jobs\Salla\Webhook\Customer\SallaCustomerUpdatedJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class CustomerUpdatedEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaCustomerUpdatedJob::dispatch(
            event: $event,
            merchantId: $merchantId,
            data: $data,
        )->onQueue(
            queue: QueueName::CUSTOMERS->value
        );
    }
}
