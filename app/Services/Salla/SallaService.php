<?php

namespace App\Services\Salla;

use Illuminate\Http\Client\Response;

abstract readonly class SallaService
{
    abstract public function validateResponse(Response $response, array $data): void;

    protected function resolve(string $name): object
    {
        $abstract = $name.':'.$this->accessToken;

        app()->singletonIf(
            abstract: $abstract,
            concrete: fn (): object => new $name(service: $this, client: $this->client),
        );

        return resolve(name: $abstract);
    }
}
