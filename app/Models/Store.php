<?php

namespace App\Models;

use App\Enums\ProviderType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected $fillable = [
        'user_id',
        'provider_type',
        'provider_id',
        'name',
        'mobile',
        'email',
        'domain',
    ];

    protected function casts(): array
    {
        return [
            'provider_type' => ProviderType::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(related: User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(related: Contact::class, foreignKey: 'store_id');
    }

    public function scopeSalla(Builder $query, ?int $providerId = null): Builder
    {
        return $query->where(column: 'provider_type', operator: '=', value: ProviderType::SALLA)->when(
            value: $providerId !== null,
            callback: fn (Builder $query): Builder => $query->where(column: 'provider_id', operator: '=', value: $providerId),
        );
    }
}
