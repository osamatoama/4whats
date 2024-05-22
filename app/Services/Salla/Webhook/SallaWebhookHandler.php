<?php

namespace App\Services\Salla\Webhook;

use App\Services\Salla\Webhook\Events\App\Store\AppStoreAuthorizeEvent;
use App\Services\Salla\Webhook\Events\App\Subscription\AppSubscriptionRenewedEvent;
use App\Services\Salla\Webhook\Events\App\Subscription\AppSubscriptionStartedEvent;
use App\Services\Salla\Webhook\Events\Customer\CustomerCreatedEvent;
use App\Services\Salla\Webhook\Events\Customer\CustomerOTPRequestEvent;
use App\Services\Salla\Webhook\Events\Customer\CustomerUpdatedEvent;
use App\Services\Salla\Webhook\Events\Order\OrderCreatedEvent;
use App\Services\Salla\Webhook\Events\Order\OrderUpdatedEvent;
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
            'order.created' => new OrderCreatedEvent(),
            'order.updated' => new OrderUpdatedEvent(),
            'customer.created' => new CustomerCreatedEvent(),
            'customer.updated' => new CustomerUpdatedEvent(),
            'customer.otp.request' => new CustomerOTPRequestEvent(),
            default => new UnknownEvent(),
        })(event: $event, merchantId: $merchantId, data: $data);
    }
}
