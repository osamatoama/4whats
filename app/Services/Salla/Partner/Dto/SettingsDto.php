<?php

namespace App\Services\Salla\Partner\Dto;

final readonly class SettingsDto
{
    public function __construct(
        public ?string $widgetMessage,
        public string $widgetColor,
        public bool $widgetIsEnabled,
    ) {
    }
}
