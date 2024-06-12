<?php

namespace App\Services\Salla\Webhook;

use App\Services\Salla\Webhook\Events\App\AppUninstalledEvent;
use App\Services\Salla\Webhook\Events\App\Settings\AppSettingsUpdatedEvent;
use App\Services\Salla\Webhook\Events\App\Store\AppStoreAuthorizeEvent;
use App\Services\Salla\Webhook\Events\App\Subscription\AppSubscriptionCanceledEvent;
use App\Services\Salla\Webhook\Events\App\Subscription\AppSubscriptionRenewedEvent;
use App\Services\Salla\Webhook\Events\App\Subscription\AppSubscriptionStartedEvent;
use App\Services\Salla\Webhook\Events\Cart\AbandonedCartEvent;
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
            'app.uninstalled' => new AppUninstalledEvent(),
            'app.subscription.started' => new AppSubscriptionStartedEvent(),
            'app.subscription.canceled' => new AppSubscriptionCanceledEvent(),
            'app.subscription.renewed' => new AppSubscriptionRenewedEvent(),
            'app.settings.updated' => new AppSettingsUpdatedEvent(),
            'order.created' => new OrderCreatedEvent(),
            'order.updated' => new OrderUpdatedEvent(),
            'customer.created' => new CustomerCreatedEvent(),
            'customer.updated' => new CustomerUpdatedEvent(),
            'customer.otp.request' => new CustomerOTPRequestEvent(),
            'abandoned.cart' => new AbandonedCartEvent(),
            default => new UnknownEvent(),
        })(event: $event, merchantId: $merchantId, data: $data);
    }
}
