<?php

namespace App\Enums\Whatsapp;

enum MessageStatus: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case SEEN = 'seen';

    public function label(): string
    {
        return __(key: 'enum.'.__CLASS__.'.'.$this->value);
    }
}
