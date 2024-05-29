<?php

namespace App\Services\Salla;

use Exception;

class SallaException extends Exception
{
    public static function fromResponse(array $data): static
    {
        return new static(
            message: "{$data['error']['code']} | {$data['error']['message']}",
            code: $data['status'],
        );
    }
}
