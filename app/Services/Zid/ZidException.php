<?php

namespace App\Services\Zid;

use Exception;
use Illuminate\Http\Client\ConnectionException;

class ZidException extends Exception
{
    public static function connectionException(ConnectionException $exception): static
    {
        return new static(
            message: $exception->getMessage(),
            code: $exception->getCode(),
            previous: $exception,
        );
    }
}
