<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MessageTemplate extends Model
{
    protected $fillable = [
        'user_id',
        'templatable_id',
        'templatable_type',
        'message',
        'placeholders',
        'delay_in_seconds',
    ];

    protected function casts(): array
    {
        return [
            'placeholders' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(related: User::class);
    }

    public function templatable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function delayInHours(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes): int => (int) $attributes['delay_in_seconds'] / 60 / 60,
        );
    }
}
