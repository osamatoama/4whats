<?php

namespace App\Services\Salla\Webhook\Events;

use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class UnknownEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        logger()->warning(message: "Salla unknown webhook event | Merchant: {$merchantId} | Event: {$event}");
        logger()->warning(message: $data);
    }
}
