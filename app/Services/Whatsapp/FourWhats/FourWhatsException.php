<?php

namespace App\Services\Whatsapp\FourWhats;

use Exception;
use Illuminate\Http\Client\ConnectionException;

class FourWhatsException extends Exception
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
