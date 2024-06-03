<?php

namespace App\Dto;

final readonly class WidgetDto
{
    public function __construct(
        public int $storeId,
        public ?string $message,
        public string $color,
        public bool $isEnabled,
    ) {
    }

    public static function fromDefault(int $storeId): self
    {
        return new self(
            storeId: $storeId,
            message: null,
            color: '#25D366',
            isEnabled: true
        );
    }
}
