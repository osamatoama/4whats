<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Str;

trait ConvertEmptyStringsToNull
{
    protected function convertEmptyStringToNull(?string $string): ?string
    {
        if ($string === null) {
            return null;
        }

        return Str::trim(value: $string) === '' ? null : $string;
    }
}
