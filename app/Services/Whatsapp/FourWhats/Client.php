<?php

namespace App\Services\Whatsapp\FourWhats;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Client
{
    public function get(string $url, array $data = []): Response
    {
        return $this->http()->get(url: $url, query: $data);
    }

    protected function http(): PendingRequest
    {
        return Http::acceptJson()->asJson();
    }
}
