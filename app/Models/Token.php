<?php

namespace App\Models;

use App\Enums\ProviderType;
use App\Services\Token\TokenService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

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

    protected function accessToken(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): string {
                if (Carbon::parse(time: $attributes['expired_at'])->lessThanOrEqualTo(date: now())) {
                    $value = (new TokenService())->updateToken(
                        token: $this,
                    );
                }

                return $value;
            },
        );
    }
}
