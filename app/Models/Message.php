<?php

namespace App\Models;

use App\Enums\Whatsapp\MessageStatus;
use App\Enums\Whatsapp\MessageType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'store_id',
        'provider_id',
        'type',
        'mobile',
        'body',
        'status',
        'attachments',
    ];

    protected function casts(): array
    {
        return [
            'type' => MessageType::class,
            'status' => MessageStatus::class,
            'attachments' => 'json',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(related: Store::class);
    }
}
