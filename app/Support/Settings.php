<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

readonly class Settings
{
    public Collection $settings;

    public function __construct(
        public ?int $storeId,
    ) {
        $this->settings = Setting::query()
            ->when(
                value: $storeId !== null,
                callback: fn (Builder $query): Builder => $query->where(column: 'store_id', operator: '=', value: $storeId),
                default: fn (Builder $query): Builder => $query->whereNull(columns: 'store_id'),
            )
            ->get();
    }

    public function find(string $key): ?Setting
    {
        return $this->settings->firstWhere(key: 'key', operator: '=', value: $key);
    }

    public function value(string $key, mixed $default = null): mixed
    {
        return $this->find(key: $key)->value ?? $default;
    }
}
