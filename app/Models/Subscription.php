<?php

namespace App\Models;

use App\Enums\ProviderType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Number;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'provider_type',
        'provider_id',
        'total_amount',
        'total_currency',
        'started_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'provider_type' => ProviderType::class,
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(related: User::class);
    }

    protected function totalAmount(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): string => Number::currency(number: $value / 100, in: $attributes['total_currency'], locale: app()->getLocale()),
        );
    }
}
