<?php

namespace App\Support;

use App\Enums\Settings\SettingsEnum;
use App\Models\Setting;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

readonly class Settings
{
    public Collection $settings;

    public function __construct(
        public ?int $storeId = null,
        protected bool $eager = true,
    ) {
        if ($this->eager) {
            $this->settings = $this->query()->get();
        } else {
            $this->settings = Collection::make();
        }
    }

    public function find(string|SettingsEnum $key): ?Setting
    {
        if ($key instanceof BackedEnum) {
            $key = $key->value;
        }

        if ($this->eager || $this->settings->contains(key: 'key', operator: '=', value: $key)) {
            return $this->settings->firstWhere(key: 'key', operator: '=', value: $key);
        }

        $setting = $this->query()->where(column: 'key', operator: '=', value: $key)->first();
        if ($setting !== null) {
            $this->settings->push(values: $setting);
        }

        return $setting;
    }

    public function value(string|SettingsEnum $key, mixed $default = null): mixed
    {
        return $this->find(key: $key)->value ?? $default;
    }

    protected function query(): Builder
    {
        return Setting::query()
            ->when(
                value: $this->storeId !== null,
                callback: fn (Builder $query): Builder => $query->where(column: 'store_id', operator: '=', value: $this->storeId),
                default: fn (Builder $query): Builder => $query->whereNull(columns: 'store_id'),
            );
    }
}
