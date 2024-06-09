<?php

namespace App\Dto;

use App\Enums\SettingKey;
use App\Models\Setting;

final readonly class SettingDto
{
    public function __construct(
        public ?int $storeId,
        public SettingKey $key,
        public ?string $value,
    ) {
    }

    public static function fromModel(Setting $setting, string $value): self
    {
        return new self(
            storeId: $setting->store_id,
            key: $setting->key,
            value: $value,
        );
    }
}
