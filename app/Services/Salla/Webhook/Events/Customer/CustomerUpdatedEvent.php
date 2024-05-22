<?php

namespace App\Services\Salla\Webhook\Events\Customer;

use App\Jobs\Salla\Webhook\Customer\SallaCustomerUpdatedJob;
use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class CustomerUpdatedEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        SallaCustomerUpdatedJob::dispatch(
            merchantId: $merchantId,
            data: $data,
        );
    }
}
