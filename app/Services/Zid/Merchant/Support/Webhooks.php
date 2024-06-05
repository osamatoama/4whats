<?php

namespace App\Services\Zid\Merchant\Support;

use App\Services\Zid\Merchant\MerchantClient;
use App\Services\Zid\Merchant\ZidMerchantException;
use App\Services\Zid\Merchant\ZidMerchantService;
use Illuminate\Http\Client\ConnectionException;

final readonly class Webhooks
{
    public function __construct(
        protected ZidMerchantService $service,
        protected MerchantClient $client,
    ) {
    }

    /**
     * @throws ZidMerchantException
     */
    public function get(): array
    {
        try {
            $response = $this->client->get(
                url: "{$this->service->baseUrl}/managers/webhooks",
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

    /**
     * @throws ZidMerchantException
     */
    public function register(int $providerId, string $event): array
    {
        try {
            $response = $this->client->post(
                url: "{$this->service->baseUrl}/managers/webhooks",
                data: [
                    'event' => $event,
                    'target_url' => route(
                        name: 'api.v1.webhooks.zid',
                        parameters: [
                            'token' => config(
                                key: 'services.zid.webhook_token',
                            ),
                            'event' => $event,
                            'store' => $providerId,
                        ],
                    ),
                    'original_id' => config(
                        key: 'services.zid.app_id',
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

    /**
     * @throws ZidMerchantException
     */
    public function delete(): array
    {
        try {
            $response = $this->client->delete(
                url: "{$this->service->baseUrl}/managers/webhooks",
                data: [
                    'original_id' => config(
                        key: 'services.zid.app_id',
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
