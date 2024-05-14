<?php

namespace App\Models;

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
        'placeholders',
        'delay_in_seconds',
        'is_enabled',
    ];

    protected function casts(): array
    {
        return [
            'placeholders' => 'array',
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
}
