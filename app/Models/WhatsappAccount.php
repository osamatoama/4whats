<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappAccount extends Model
{
    protected $fillable = [
        'store_id',
        'label',
        'connected_mobile',
        'instance_id',
        'instance_token',
        'is_sending_enabled',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'expired_at' => 'datetime',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(related: Store::class);
    }

    protected function isSendingDisabled(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): bool => ! $this->is_sending_enabled,
        );
    }

    protected function isExpired(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->expired_at->lessThanOrEqualTo(date: now()),
        );
    }
}
