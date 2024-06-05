<?php

namespace App\Services\Zid\Merchant;

use App\Services\Zid\Merchant\Support\AbandonedCarts;
use App\Services\Zid\Merchant\Support\Customers;
use App\Services\Zid\Merchant\Support\Webhooks;
use Illuminate\Http\Client\Response;

final readonly class ZidMerchantService
{
    public string $baseUrl;

    public readonly MerchantClient $client;

    public function __construct(
        protected string $managerToken,
        protected string $accessToken,
    ) {
        $this->baseUrl = 'https://api.zid.sa/v1';

        $this->client = new MerchantClient(
            managerToken: $this->managerToken,
            accessToken: $this->accessToken,
        );
    }

    public function abandonedCarts(): AbandonedCarts
    {
        return $this->resolve(
            name: AbandonedCarts::class,
        );
    }

    public function customers(): Customers
    {
        return $this->resolve(
            name: Customers::class,
        );
    }

    public function webhooks(): Webhooks
    {
        return $this->resolve(
            name: Webhooks::class,
        );
    }

    /**
     * @throws ZidMerchantException
     */
    public function validateResponse(Response $response, array $data): void
    {
        if ($response->failed()) {
            throw ZidMerchantException::fromResponse(
                response: $response,
                data: $data,
            );
        }
    }

    protected function resolve(string $name): object
    {
        $abstract = $name.':'.$this->managerToken.':'.$this->accessToken;

        return resolveSingletonIf(
            abstract: $abstract,
            concrete: fn (): object => new $name(service: $this, client: $this->client),
        );
    }
}
