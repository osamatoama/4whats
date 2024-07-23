<?php

namespace App\Dto;

use App\Enums\IncomingWebhookProviderType;

final readonly class IncomingWebhookDto
{
    public function __construct(
        public IncomingWebhookProviderType $providerType,
        public array $payload,
    ) {
    }

    public static function fromSalla(array $payload): self
    {
        return new self(
            providerType: IncomingWebhookProviderType::SALLA,
            payload: $payload,
        );
    }

    public static function fromZid(array $payload): self
    {
        return new self(
            providerType: IncomingWebhookProviderType::ZID,
            payload: $payload,
        );
    }

    public static function fromFourWhats(array $payload): self
    {
        return new self(
            providerType: IncomingWebhookProviderType::FOUR_WHATS,
            payload: $payload,
        );
    }
}
