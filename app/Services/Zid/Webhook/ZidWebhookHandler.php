<?php

namespace App\Services\Zid\Webhook;

use App\Services\Zid\Webhook\Events\App\Market\Application\UninstallEvent;
use App\Services\Zid\Webhook\Events\UnknownEvent;

class ZidWebhookHandler
{
    public function isVerified(string $token, int $appId): bool
    {
        return $token == config(key: 'services.zid.webhook_token') && $appId == config(key: 'services.zid.app_id');
    }

    public function isNotVerified(string $token, int $appId): bool
    {
        return ! $this->isVerified(token: $token, appId: $appId);
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
