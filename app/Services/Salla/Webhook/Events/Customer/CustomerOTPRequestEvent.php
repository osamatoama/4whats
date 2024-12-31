<?php

namespace App\Services\Salla\Webhook\Events\Customer;

use App\Enums\Jobs\QueueName;
use App\Jobs\Salla\Webhook\Customer\SallaCustomerOTPRequestJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class CustomerOTPRequestEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaCustomerOTPRequestJob::dispatch(
            event: $event,
            merchantId: $merchantId,
            data: $data,
        )->onQueue(
            queue: QueueName::CUSTOMERS->value
        );
    }
}
