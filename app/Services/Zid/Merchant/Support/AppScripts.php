<?php

namespace App\Services\Zid\Merchant\Support;

use App\Services\Zid\Merchant\MerchantClient;
use App\Services\Zid\Merchant\ZidMerchantException;
use App\Services\Zid\Merchant\ZidMerchantService;
use Illuminate\Http\Client\ConnectionException;

class AppScripts
{
    public function __construct(
        protected ZidMerchantService $service,
        protected MerchantClient $client,
    ) {
    }

    /**
     * @throws ZidMerchantException
     */
    public function create(): array
    {
        try {
            $response = $this->client->post(
                url: "{$this->service->baseUrl}/managers/app-scripts",
                data: [
                    'url' => asset(
                        path: 'assets/zid/js/widget.js',
                    ),
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
}
