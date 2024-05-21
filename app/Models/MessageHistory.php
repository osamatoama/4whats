<?php

namespace App\Models;

use App\Enums\Whatsapp\MessageStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageHistory extends Model
{
    protected $fillable = [
        'store_id',
        'provider_id',
        'message',
        'to',
        'status',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => MessageStatus::class,
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(related: Store::class);
    }
}
