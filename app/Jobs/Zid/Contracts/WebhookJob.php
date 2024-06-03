<?php

namespace App\Jobs\Zid\Contracts;

interface WebhookJob
{
    public function __construct(
        string $event,
        int $providerId,
        array $data,
    );
}
