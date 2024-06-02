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
        'total_amount',
        'total_currency',
        'checkout_url',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'provider_type' => ProviderType::class,
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
