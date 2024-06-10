<?php

namespace App\Enums\Concerns;

trait HasLabel
{
    public function label(): string
    {
        return __(
            key: 'enum.'.__CLASS__.'.'.$this->name,
        );
    }
}
