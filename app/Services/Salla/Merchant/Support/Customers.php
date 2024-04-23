<?php

namespace App\Services\Salla\Merchant\Support;

use App\Services\Salla\Merchant\Client;
use App\Services\Salla\Merchant\SallaMerchantException;
use App\Services\Salla\Merchant\SallaMerchantService;

class Customers
{
    protected string $baseUrl = 'https://api.salla.dev/admin/v2/customers';

    public function __construct(
        protected SallaMerchantService $service,
        protected Client $client,
    ) {
    }

    /**
     * @throws SallaMerchantException
     */
    public function get(int $page = 1): array
    {
        $response = $this->client->get(url: $this->baseUrl, data: [
            'page' => $page,
        ]);

        $data = $response->json();

        $this->service->validateResponse(response: $response, data: $data);

        return $data;
    }
}
