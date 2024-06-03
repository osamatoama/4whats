<?php

namespace App\Services\Zid\Webhook\Contracts;

interface WebhookEvent
{
    public function __invoke(string $event, int $providerId, array $data): void;
}
