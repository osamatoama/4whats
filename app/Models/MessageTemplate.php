<?php

namespace App\Models;

use App\Enums\StoreMessageTemplate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageTemplate extends Model
{
    protected $fillable = [
        'store_id',
        'key',
        'message',
        'delay_in_seconds',
        'is_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(related: Store::class);
    }

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where(column: 'is_enabled', operator: '=', value: true);
    }

    public function scopeDisabled(Builder $query): Builder
    {
        return $query->where(column: 'is_enabled', operator: '=', value: false);
    }

    public function scopeKey(Builder $query, string|StoreMessageTemplate $key): Builder
    {
        if ($key instanceof StoreMessageTemplate) {
            $key = $key->value;
        }

        return $query->where(column: 'key', operator: '=', value: $key);
    }

    protected function isDisabled(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): bool => ! $this->is_enabled,
        );
    }

    protected function delayInHours(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): int => (int) $attributes['delay_in_seconds'] / 60 / 60,
        );
    }

    protected function enum(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): StoreMessageTemplate {
                if (str(string: $attributes['key'])->startsWith(needles: StoreMessageTemplate::ORDER_STATUSES->value)) {
                    return StoreMessageTemplate::ORDER_STATUSES;
                }

                return StoreMessageTemplate::from(value: $attributes['key']);
            },
        );
    }

    protected function isReviewOrder(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): bool => in_array(needle: $attributes['key'], haystack: StoreMessageTemplate::reviewOrderValues()),
        );
    }

    protected function isNewOrderForEmployees(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): bool => in_array(needle: $attributes['key'], haystack: StoreMessageTemplate::newOrderForEmployeesValues()),
        );
    }

    protected function isOrderStatus(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): bool => str(string: $attributes['key'])->beforeLast(search: '.')->toString() === StoreMessageTemplate::ORDER_STATUSES->value,
        );
    }

    protected function orderStatusId(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): int => (int) str(string: $attributes['key'])->afterLast(search: '.')->toString(),
        );
    }

    protected function orderStatus(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): OrderStatus => currentStore()->orderStatuses->firstWhere(key: 'id', operator: '=', value: $this->order_status_id),
        );
    }
}
