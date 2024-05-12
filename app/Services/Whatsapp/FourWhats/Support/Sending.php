<?php

namespace App\Services\Whatsapp\FourWhats\Support;

use App\Services\Whatsapp\FourWhats\Client;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;

class Sending
{
    protected string $baseUrl = 'https://api.4whats.net';

    public function __construct(
        protected FourWhatsService $service,
        protected Client $client,
        protected int $instanceId,
        protected string $instanceToken,
    ) {
    }

    /**
     * @throws FourWhatsException
     */
    public function text(string $mobile, string $message): array
    {
        $response = $this->client->get(
            url: $this->baseUrl.'/sendMessage',
            data: [
                'instanceid' => $this->instanceId,
                'token' => $this->instanceToken,
                'phone' => $mobile,
                'body' => $message,
            ],
        );

        $data = $response->json();

        $this->client->validateResponse(data: $data);

        return [
            'id' => $data['id'],
        ];
    }
}
