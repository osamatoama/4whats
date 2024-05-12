<?php

namespace App\Models;

use App\Enums\ProviderType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class OrderStatus extends Model
{
    protected $fillable = [
        'order_status_id',
        'store_id',
        'provider_type',
        'provider_id',
        'name',
    ];

    protected function casts(): array
    {
        return [
            'provider_type' => ProviderType::class,
        ];
    }

    public function orderStatus(): BelongsTo
    {
        return $this->belongsTo(related: OrderStatus::class);
    }

    public function orderStatuses(): HasMany
    {
        return $this->hasMany(related: OrderStatus::class, foreignKey: 'order_status_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(related: Store::class);
    }

    public function template(): MorphOne
    {
        return $this->morphOne(related: MessageTemplate::class, name: 'templatable');
    }
}
