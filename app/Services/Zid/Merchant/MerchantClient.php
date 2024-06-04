<?php

namespace App\Services\Zid\Merchant;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final readonly class MerchantClient
{
    public function __construct(
        protected string $managerToken,
        protected string $accessToken,
    ) {
    }

    /**
     * @throws ConnectionException
     */
    public function get(string $url, array $query = []): Response
    {
        return $this->http()->get(
            url: $url,
            query: $query,
        );
    }

    /**
     * @throws ConnectionException
     */
    public function post(string $url, array $data = []): Response
    {
        return $this->http()->post(
            url: $url,
            data: $data,
        );
    }

    /**
     * @throws ConnectionException
     */
    public function put(string $url, array $data = []): Response
    {
        return $this->http()->put(
            url: $url,
            data: $data,
        );
    }

    /**
     * @throws ConnectionException
     */
    public function patch(string $url, array $data = []): Response
    {
        return $this->http()->patch(
            url: $url,
            data: $data,
        );
    }

    /**
     * @throws ConnectionException
     */
    public function delete(string $url, array $data = []): Response
    {
        return $this->http()->delete(
            url: $url,
            data: $data,
        );
    }

    protected function http(): PendingRequest
    {
        return Http::withHeaders(
            headers: [
                'Accept-Language' => app()->getLocale(),
                'X-Manager-Token' => $this->managerToken,
            ],
        )->withToken(
            token: $this->accessToken,
        )->acceptJson()->asJson();
    }
}
