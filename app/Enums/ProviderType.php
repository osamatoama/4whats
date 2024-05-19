<?php

namespace App\Enums;

enum ProviderType: string
{
    case SALLA = 'salla';

    public function label(): string
    {
        return __(key: 'enum.'.__CLASS__.'.'.$this->name);
    }
}
