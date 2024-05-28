<?php

namespace App\Services\Salla\Webhook\Events;

use App\Services\Salla\Webhook\Contracts\SallaWebhookEvent;

class UnknownEvent implements SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void
    {
        $excluded = [
            'app.installed',
        ];

        if (in_array(needle: $event, haystack: $excluded)) {
            return;
        }

        $json = json_encode(
            value: $data,
            flags: JSON_UNESCAPED_UNICODE,
        );

        logger()->warning(
            message: generateMessageUsingSeparatedLines(
                lines: [
                    'Salla unknown webhook event',
                    "Event: {$event}",
                    "Merchant: {$merchantId}",
                    $json,
                ],
            ),
        );
    }
}
