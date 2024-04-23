<?php

namespace App\Services\Salla\Webhook\Contracts;

interface SallaWebhookEvent
{
    public function handle(int $merchantId, array $data): void;
}
