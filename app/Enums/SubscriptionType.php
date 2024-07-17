<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum SubscriptionType: string
{
    use HasLabel;

    case NONE = 'none';
    case TRIAL = 'trial';
    case PAID = 'paid';

    public function cssClass(): string
    {
        return match ($this) {
            self::NONE => 'danger',
            self::TRIAL => 'warning',
            self::PAID => 'success',
        };
    }
}
