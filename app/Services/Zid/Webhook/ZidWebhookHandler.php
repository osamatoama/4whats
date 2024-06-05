<?php

namespace App\Services\Zid\Webhook;

use App\Services\Zid\Webhook\Events\App\Market\Application\UninstallEvent;
use App\Services\Zid\Webhook\Events\UnknownEvent;

class ZidWebhookHandler
{
    public function isVerified(string $token): bool
    {
        return $token == config(key: 'services.zid.webhook_token');
    }

    public function isNotVerified(string $token): bool
    {
        return ! $this->isVerified(
            token: $token,
        );
    }

    public function handle(string $event, int $providerId, array $data): void
    {
        (match ($event) {
            'app.market.application.uninstall' => new UninstallEvent(),
            default => new UnknownEvent(),
        })(
            event: $event,
            providerId: $providerId,
            data: $data,
        );
    }
}
