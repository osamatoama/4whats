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

    /**
     * @throws FourWhatsException
     */
    public function validateResponse(array $data): void
    {
        if (isset($data['success']) && $data['success'] === false) {
            throw new FourWhatsException(message: $data['reason'] ?? $data['msg']);
        }
    }

    protected function http(): PendingRequest
    {
        return Http::acceptJson()->asJson();
    }
}
