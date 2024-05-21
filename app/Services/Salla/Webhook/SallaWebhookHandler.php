<?php

namespace App\Services\Salla\Webhook;

use App\Services\Salla\Webhook\Events\App\Store\AppStoreAuthorizeEvent;
use App\Services\Salla\Webhook\Events\App\Subscription\AppSubscriptionRenewedEvent;
use App\Services\Salla\Webhook\Events\App\Subscription\AppSubscriptionStartedEvent;
use App\Services\Salla\Webhook\Events\UnknownEvent;

class SallaWebhookHandler
{
    public function isVerified(string $token): bool
    {
        return $token === config(key: 'services.salla.webhook_token');
    }

    public function isNotVerified(string $token): bool
    {
        return ! $this->isVerified(token: $token);
    }

    public function handle(string $event, int $merchantId, array $data): void
    {
        (match ($event) {
            'app.store.authorize' => new AppStoreAuthorizeEvent(),
            'app.subscription.started' => new AppSubscriptionStartedEvent(),
            'app.subscription.renewed' => new AppSubscriptionRenewedEvent(),
            default => new UnknownEvent(),
        })(event: $event, merchantId: $merchantId, data: $data);
    }
}
