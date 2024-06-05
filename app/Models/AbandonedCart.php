<?php

namespace App\Models;

use App\Enums\ProviderType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Number;

class AbandonedCart extends Model
{
    protected $fillable = [
        'store_id',
        'contact_id',
        'provider_type',
        'provider_id',
        'provider_created_at',
        'provider_updated_at',
        'total_amount',
        'total_currency',
        'checkout_url',
    ];

    protected function casts(): array
    {
        return [
            'provider_type' => ProviderType::class,
            'provider_created_at' => 'datetime',
            'provider_updated_at' => 'datetime',
        ];
    }

    protected function totalAmount(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): string => Number::format(number: $value / 100, locale: app()->getLocale()),
        );
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(related: Store::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(related: Contact::class);
    }
}
