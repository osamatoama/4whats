<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $fillable = [
        'store_id',
        'key',
        'value',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(related: Store::class);
    }
}
