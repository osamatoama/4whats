<?php

namespace App\Services\Whatsapp\FourWhats\Support;

use App\Services\Whatsapp\FourWhats\Client;
use App\Services\Whatsapp\FourWhats\Contracts\Support\Sending as SendingContract;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;

class Sending implements SendingContract
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

        if (isset($data['success']) && $data['success'] === false) {
            throw new FourWhatsException(message: $data['reason'] ?? '');
        }

        return [
            'id' => $data['id'],
        ];
    }
}
