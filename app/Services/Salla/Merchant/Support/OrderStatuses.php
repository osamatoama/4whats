<?php

namespace App\Services\Salla\Merchant\Support;

use App\Services\Salla\Merchant\SallaMerchantClient;
use App\Services\Salla\Merchant\SallaMerchantException;
use App\Services\Salla\Merchant\SallaMerchantService;

final readonly class OrderStatuses
{
    protected string $baseUrl;

    public function __construct(
        protected SallaMerchantService $service,
        protected SallaMerchantClient $client,
    ) {
        $this->baseUrl = 'https://api.salla.dev/admin/v2/orders/statuses';
    }

    /**
     * @throws SallaMerchantException
     */
    public function get(): array
    {
        $response = $this->client->get(url: $this->baseUrl);

        $data = $response->json();

        $this->service->validateResponse(response: $response, data: $data);

        return $data;
    }
}
