<?php

namespace App\Services\Salla;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;

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

    public static function sallaServerError(Response $response, ?array $data = null): static
    {
        $data ??= $response->json();

        return new static(
            message: "{$data['message']}",
            code: 500,
        );
    }

    public static function fromResponse(Response $response, ?array $data = null): static
    {
        $data ??= $response->json();

        return new static(
            message: "{$data['error']['code']} | {$data['error']['message']}",
            code: $data['status'],
        );
    }

    public static function fromNullData(Response $response): static
    {
        $exception = $response->toException();

        logger()->warning(
            message: implode(
                separator: PHP_EOL,
                array: [
                    'Salla exception with null data',
                    "Message: {$exception->getMessage()}",
                    "Code: {$exception->getCode()}",
                ],
            ),
        );

        return new static(
            message: $exception->getMessage(),
            code: $exception->getCode(),
            previous: $exception,
        );
    }

    public static function withLines(SallaException $exception, array $lines): static
    {
        return new static(
            message: implode(
                separator: PHP_EOL,
                array: array_merge(
                    $lines,
                    [
                        "Reason: {$exception->getMessage()}",
                    ],
                ),
            ),
            code: $exception->getCode(),
            previous: $exception,
        );
    }
}
