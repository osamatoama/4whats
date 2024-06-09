<?php

namespace App\Dto;

final readonly class WidgetDto
{
    public function __construct(
        public int $storeId,
        public string $mobile,
        public ?string $message,
        public string $color,
        public bool $isEnabled,
    ) {
    }

    public static function fromDefault(int $storeId, string $mobile): self
    {
        return new self(
            storeId: $storeId,
            mobile: $mobile,
            message: null,
            color: '#25D366',
            isEnabled: true
        );
    }

    public static function fromSallaWebhook(int $storeId, array $settings): self
    {
        return new self(
            storeId: $storeId,
            mobile: $settings['widget_mobile'],
            message: $settings['widget_message'],
            color: $settings['widget_color'],
            isEnabled: $settings['widget_is_enabled']
        );
    }
}
