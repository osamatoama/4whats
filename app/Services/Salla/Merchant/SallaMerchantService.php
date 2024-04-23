<?php

namespace App\Services\Salla\Merchant;

use App\Services\Salla\Merchant\Support\Customers;
use Illuminate\Http\Client\Response;

class SallaMerchantService
{
    public readonly Client $client;

    public function __construct(
        protected string $accessToken,
    ) {
        $this->client = new Client(accessToken: $this->accessToken);
    }

    public function customers(): Customers
    {
        $abstract = Customers::class.':'.$this->accessToken;

        app()->singletonIf($abstract, fn (): Customers => new Customers(service: $this, client: $this->client));

        return app($abstract);
    }

    /**
     * @throws SallaMerchantException
     */
    public function validateResponse(Response $response, array $data): void
    {
        if ($response->failed()) {
            throw new SallaMerchantException(message: "{$data['error']['code']} | {$data['error']['message']}");
        }
    }
}
