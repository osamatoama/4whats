<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum ProviderType: string
{
    use HasLabel;

    case SALLA = 'salla';
    case ZID = 'zid';
}
