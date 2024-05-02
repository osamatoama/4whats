<?php

namespace App\Services\Salla\Merchant\Support;

use App\Services\Salla\Merchant\Client;
use App\Services\Salla\Merchant\SallaMerchantException;
use App\Services\Salla\Merchant\SallaMerchantService;

class OrderStatuses
{
    protected string $baseUrl = 'https://api.salla.dev/admin/v2/orders/statuses';

    public function __construct(
        protected SallaMerchantService $service,
        protected Client $client,
    ) {
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
