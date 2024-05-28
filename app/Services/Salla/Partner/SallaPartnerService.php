<?php

namespace App\Services\Salla\Partner;

use App\Services\Salla\Partner\Support\Settings;
use App\Services\Salla\SallaService;
use Illuminate\Http\Client\Response;

final readonly class SallaPartnerService extends SallaService
{
    public SallaPartnerClient $client;

    public function __construct(
        protected string $accessToken,
    ) {
        $this->client = new SallaPartnerClient(accessToken: $this->accessToken);
    }

    public function settings(): Settings
    {
        return $this->resolve(name: Settings::class);
    }

    /**
     * @throws SallaPartnerException
     */
    public function validateResponse(Response $response, array $data): void
    {
        if ($response->failed()) {
            throw new SallaPartnerException(
                message: "{$data['error']['code']} | {$data['error']['message']}",
                code: $data['status'],
            );
        }
    }
}
