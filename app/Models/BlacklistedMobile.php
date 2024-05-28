<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlacklistedMobile extends Model
{
    protected $fillable = [
        'store_id',
        'mobile',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(related: Store::class);
    }

    public function scopeMobile(Builder $query, string $mobile): Builder
    {
        return $query->where(column: 'mobile', operator: '=', value: $mobile);
    }
}
