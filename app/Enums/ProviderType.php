<?php

namespace App\Enums;

enum ProviderType: string
{
    case SALLA = 'salla';
    case ZID = 'zid';

    public function label(): string
    {
        return __(key: 'enum.'.__CLASS__.'.'.$this->name);
    }
}
