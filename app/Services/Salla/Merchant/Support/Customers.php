<?php

namespace App\Services\Salla\Merchant\Support;

use App\Services\Salla\Merchant\SallaMerchantClient;
use App\Services\Salla\Merchant\SallaMerchantException;
use App\Services\Salla\Merchant\SallaMerchantService;

final readonly class Customers
{
    protected string $baseUrl;

    public function __construct(
        protected SallaMerchantService $service,
        protected SallaMerchantClient $client,
    ) {
        $this->baseUrl = 'https://api.salla.dev/admin/v2/customers';
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
