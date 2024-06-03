<?php

namespace App\Services\Zid\Webhook\Events;

use App\Services\Zid\Webhook\Contracts\WebhookEvent;

class UnknownEvent implements WebhookEvent
{
    public function __invoke(string $event, int $providerId, array $data): void
    {
        $excluded = [
            'app.market.application.install',
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
                    'Zid unknown webhook event',
                    "Event: {$event}",
                    "ProviderID: {$providerId}",
                    $json,
                ],
            ),
        );
    }
}
