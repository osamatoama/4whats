<?php

namespace App\Models;

use App\Enums\ContactSource;
use App\Enums\ProviderType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'store_id',
        'provider_type',
        'provider_id',
        'source',
        'first_name',
        'last_name',
        'email',
        'mobile',
        'gender',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'provider_type' => ProviderType::class,
            'source' => ContactSource::class,
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(related: Store::class);
    }

    public function abandonedCarts(): HasMany
    {
        return $this->hasMany(related: AbandonedCart::class, foreignKey: 'contact_id');
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): string => $attributes['first_name'].' '.$attributes['last_name'],
        );
    }
}
