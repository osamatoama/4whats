<?php

namespace App\Services\Salla\Merchant;

use App\Services\Salla\Merchant\Support\AbandonedCarts;
use App\Services\Salla\Merchant\Support\Customers;
use App\Services\Salla\Merchant\Support\OrderStatuses;
use App\Services\Salla\SallaService;
use Illuminate\Http\Client\Response;

final readonly class SallaMerchantService extends SallaService
{
    public SallaMerchantClient $client;

    public function __construct(
        protected string $accessToken,
    ) {
        $this->client = new SallaMerchantClient(accessToken: $this->accessToken);
    }

    public function orderStatuses(): OrderStatuses
    {
        return $this->resolve(name: OrderStatuses::class);
    }

    public function customers(): Customers
    {
        return $this->resolve(name: Customers::class);
    }

    public function abandonedCarts(): AbandonedCarts
    {
        return $this->resolve(name: AbandonedCarts::class);
    }

    /**
     * @throws SallaMerchantException
     */
    public function validateResponse(Response $response, ?array $data): void
    {
        if ($response->failed()) {
            if ($data === null) {
                throw SallaMerchantException::fromNullData(
                    response: $response,
                );
            }

            if ($response->status() === 500) {
                throw SallaMerchantException::sallaServerError(
                    response: $response,
                    data: $data,
                );
            }

            throw SallaMerchantException::fromResponse(
                response: $response,
                data: $data,
            );
        }
    }
}
