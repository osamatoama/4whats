<?php

namespace App\Services\Salla\Merchant;

use App\Services\Salla\Merchant\Support\AbandonedCarts;
use App\Services\Salla\Merchant\Support\Customers;
use App\Services\Salla\Merchant\Support\OrderStatuses;
use Illuminate\Http\Client\Response;

class SallaMerchantService
{
    public readonly Client $client;

    public function __construct(
        protected string $accessToken,
    ) {
        $this->client = new Client(accessToken: $this->accessToken);
    }

    public function orderStatuses(): OrderStatuses
    {
        $abstract = OrderStatuses::class.':'.$this->accessToken;

        app()->singletonIf($abstract, fn (): OrderStatuses => new OrderStatuses(service: $this, client: $this->client));

        return app($abstract);
    }

    public function customers(): Customers
    {
        $abstract = Customers::class.':'.$this->accessToken;

        app()->singletonIf($abstract, fn (): Customers => new Customers(service: $this, client: $this->client));

        return app($abstract);
    }

    public function abandonedCarts(): AbandonedCarts
    {
        $abstract = AbandonedCarts::class.':'.$this->accessToken;

        app()->singletonIf($abstract, fn (): AbandonedCarts => new AbandonedCarts(service: $this, client: $this->client));

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
