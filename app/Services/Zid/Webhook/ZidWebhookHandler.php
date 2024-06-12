<?php

namespace App\Services\Zid\Webhook;

use App\Services\Zid\Webhook\Events\AbandonedCart\AbandonedCartCompletedEvent;
use App\Services\Zid\Webhook\Events\AbandonedCart\AbandonedCartCreatedEvent;
use App\Services\Zid\Webhook\Events\App\Market\Application\InstallEvent;
use App\Services\Zid\Webhook\Events\App\Market\Application\UninstallEvent;
use App\Services\Zid\Webhook\Events\Customer\CustomerCreateEvent;
use App\Services\Zid\Webhook\Events\Customer\CustomerUpdateEvent;
use App\Services\Zid\Webhook\Events\Order\OrderCreateEvent;
use App\Services\Zid\Webhook\Events\Order\OrderStatusUpdateEvent;
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
            'app.market.application.install' => new InstallEvent(),
            'app.market.application.uninstall' => new UninstallEvent(),
            'order.create' => new OrderCreateEvent(),
            'order.status.update' => new OrderStatusUpdateEvent(),
            'abandoned_cart.created' => new AbandonedCartCreatedEvent(),
            'abandoned_cart.completed' => new AbandonedCartCompletedEvent(),
            'customer.create' => new CustomerCreateEvent(),
            'customer.update' => new CustomerUpdateEvent(),
            default => new UnknownEvent(),
        })(
            event: $event,
            providerId: $providerId,
            data: $data,
        );
    }
}
