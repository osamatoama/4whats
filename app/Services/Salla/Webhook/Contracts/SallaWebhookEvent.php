<?php

namespace App\Services\Salla\Webhook\Contracts;

interface SallaWebhookEvent
{
    public function __invoke(string $event, int $merchantId, array $data): void;
}
