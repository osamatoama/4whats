<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Widget extends Model
{
    protected $fillable = [
        'store_id',
        'message',
        'color',
        'is_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(related: Store::class);
    }
}
