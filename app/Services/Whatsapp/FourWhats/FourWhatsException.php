<?php

namespace App\Services\Whatsapp\FourWhats;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Throwable;

class FourWhatsException extends Exception
{
    public readonly ?string $errorCode;

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null, ?string $errorCode = null)
    {
        $this->errorCode = $errorCode;

        parent::__construct(
            message: $message,
            code: $code,
            previous: $previous,
        );
    }

    public static function connectionException(ConnectionException $exception): static
    {
        return new static(
            message: $exception->getMessage(),
            code: $exception->getCode(),
            previous: $exception,
        );
    }
}
