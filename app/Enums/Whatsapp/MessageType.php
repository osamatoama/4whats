<?php

namespace App\Enums\Whatsapp;

enum MessageType: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case VIDEO = 'video';
    case AUDIO = 'audio';

    public function label(): string
    {
        return __(
            key: 'enum.'.__CLASS__.'.'.$this->name,
        );
    }
}
