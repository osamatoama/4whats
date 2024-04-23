<?php

namespace App\Models;

use App\Enums\ContactSource;
use App\Enums\ProviderType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $fillable = [
        'store_id',
        'provider_type',
        'provider_id',
        'source',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
    ];

    protected function casts(): array
    {
        return [
            'provider_type' => ProviderType::class,
            'source' => ContactSource::class,
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(related: Store::class);
    }
}
