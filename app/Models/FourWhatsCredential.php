<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FourWhatsCredential extends Model
{
    protected $fillable = [
        'user_id',
        'provider_id',
        'email',
        'mobile',
        'api_key',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(related: User::class);
    }
}
