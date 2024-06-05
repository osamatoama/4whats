<?php

namespace App\Models;

use App\Enums\ProviderType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Token extends Model
{
    protected $fillable = [
        'provider_type',
        'access_token',
        'refresh_token',
        'expired_at',
        'manager_token',
    ];

    protected function casts(): array
    {
        return [
            'provider_type' => ProviderType::class,
            'expired_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(related: User::class);
    }
}
