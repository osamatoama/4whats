<?php

namespace App\Services\Salla\Merchant;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Client
{
    public function __construct(
        protected string $accessToken,
    ) {
    }

    public function get(string $url, array $data = []): Response
    {
        return $this->http()->get(url: $url, query: $data);
    }

    public function post(string $url, array $data = []): Response
    {
        return $this->http()->post(url: $url, data: $data);
    }

    public function put(string $url, array $data = []): Response
    {
        return $this->http()->put(url: $url, data: $data);
    }

    public function patch(string $url, array $data = []): Response
    {
        return $this->http()->patch(url: $url, data: $data);
    }

    public function delete(string $url, array $data = []): Response
    {
        return $this->http()->delete(url: $url, data: $data);
    }

    protected function http(): PendingRequest
    {
        return Http::withToken(token: $this->accessToken)->acceptJson()->asJson();
    }
}
