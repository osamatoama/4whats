<?php

namespace App\Enums\Whatsapp;

use App\Enums\Concerns\HasLabel;

enum MessageType: string
{
    use HasLabel;

    case TEXT = 'text';
    case FILE = 'file';
    case IMAGE = 'image';
    case VIDEO = 'video';
    case AUDIO = 'audio';
}
