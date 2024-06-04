<?php

namespace App\Services\Zid\Merchant\Support;

use App\Services\Zid\Merchant\MerchantClient;
use App\Services\Zid\Merchant\ZidMerchantException;
use App\Services\Zid\Merchant\ZidMerchantService;
use Illuminate\Http\Client\ConnectionException;

final readonly class Customers
{
    public function __construct(
        protected ZidMerchantService $service,
        protected MerchantClient $client,
    ) {
    }

    /**
     * @throws ZidMerchantException
     */
    public function get(int $page = 1, int $perPage = 15): array
    {
        try {
            $response = $this->client->get(
                url: "{$this->service->baseUrl}/managers/store/customers",
                query: [
                    'page' => $page,
                    'per_page' => $perPage,
                ],
            );
        } catch (ConnectionException $e) {
            throw ZidMerchantException::connectionException(
                exception: $e,
            );
        }

        $data = $response->json();

        $this->service->validateResponse(
            response: $response,
            data: $data,
        );

        return $data;
    }

    public function getTotalPages(array $data, int $perPage = 15): int
    {
        return ceil(
            num: $data['total_customers_count'] / $perPage,
        );
    }
}
