<?php

namespace App\Services\Salla;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;

abstract readonly class SallaService
{
    abstract public function validateResponse(Response $response, array $data): void;

    public static function parseDate(array $data): Carbon
    {
        return Carbon::parse(time: $data['date'], timezone: $data['timezone'])->timezone(value: config(key: 'app.timezone'));
    }

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
