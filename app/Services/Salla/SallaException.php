<?php

namespace App\Services\Salla;

use Exception;
use Illuminate\Http\Client\ConnectionException;

class SallaException extends Exception
{
    public static function connectionException(ConnectionException $exception): static
    {
        return new static(
            message: $exception->getMessage(),
            code: $exception->getCode(),
            previous: $exception,
        );
    }

    public static function fromResponse(array $data): static
    {
        return new static(
            message: "{$data['error']['code']} | {$data['error']['message']}",
            code: $data['status'],
        );
    }
}
