<?php

namespace App\Enums\Whatsapp;

use App\Enums\Concerns\HasLabel;

enum MessageStatus: string
{
    use HasLabel;

    case PENDING = 'pending';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case VIEWED = 'viewed';
    case PLAYED = 'played';
}
