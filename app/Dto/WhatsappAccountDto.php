<?php

namespace App\Dto;

use Illuminate\Support\Carbon;

final readonly class WhatsappAccountDto
{
    public function __construct(
        public int $storeId,
        public string $label,
        public ?string $connectedMobile,
        public int $instanceId,
        public string $instanceToken,
        public bool $isSendingEnabled,
        public Carbon $expiredAt,
    ) {
    }

    public static function fromExpired(int $storeId, string $label): self
    {
        return new self(
            storeId: $storeId,
            label: $label,
            connectedMobile: null,
            instanceId: 0,
            instanceToken: '',
            isSendingEnabled: true,
            expiredAt: now()->subSecond(),
        );
    }
}
