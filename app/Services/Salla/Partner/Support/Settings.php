<?php

namespace App\Services\Salla\Partner\Support;

use App\Services\Salla\Partner\SallaPartnerClient;
use App\Services\Salla\Partner\SallaPartnerException;
use App\Services\Salla\Partner\SallaPartnerService;
use Illuminate\Http\Client\ConnectionException;

final readonly class Settings
{
    protected string $baseUrl;

    public function __construct(
        protected SallaPartnerService $service,
        protected SallaPartnerClient $client,
    ) {
        $this->baseUrl = 'https://api.salla.dev/admin/v2/apps/'.config(key: 'services.salla.app_id').'/settings';
    }

    /**
     * @throws SallaPartnerException
     */
    public function update(array $data): array
    {
        try {
            $response = $this->client->post(
                url: $this->baseUrl,
                data: $data,
            );
        } catch (ConnectionException $e) {
            throw SallaPartnerException::connectionException(
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
