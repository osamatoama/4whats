<?php

namespace App\Dto;

use App\Enums\SettingKey;

final readonly class SettingDto
{
    public function __construct(
        public int $storeId,
        public SettingKey $settingKey,
        public ?string $value,
    ) {
    }
}
