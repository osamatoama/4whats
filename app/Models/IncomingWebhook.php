<?php

namespace App\Models;

use App\Enums\IncomingWebhookProviderType;
use Illuminate\Database\Eloquent\Model;

class IncomingWebhook extends Model
{
    protected $fillable = [
        'provider_type',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'provider_type' => IncomingWebhookProviderType::class,
            'payload' => 'json',
        ];
    }
}
