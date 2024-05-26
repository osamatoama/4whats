<?php

namespace App\Services\Salla\Webhook\Events;

use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class UnknownEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        $json = json_encode(
            value: $data,
            flags: JSON_UNESCAPED_UNICODE,
        );

        logger()->warning(message: 'Salla unknown webhook event'.PHP_EOL."Event: {$event}".PHP_EOL."Merchant: {$merchantId}".PHP_EOL.$json);
    }
}
