<?php

namespace App\Services\Whatsapp\FourWhats\Support;

use App\Services\Whatsapp\FourWhats\Client;
use App\Services\Whatsapp\FourWhats\Contracts\Support\Webhook as WebhookContract;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use Illuminate\Http\Client\ConnectionException;

class Webhook implements WebhookContract
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
    public function set(string $url): array
    {
        try {
            $response = $this->client->get(
                url: $this->baseUrl.'/webhook',
                data: [
                    'instanceid' => $this->instanceId,
                    'token' => $this->instanceToken,
                    'webhookUrl' => $url,
                ],
            );
        } catch (ConnectionException $e) {
            throw FourWhatsException::connectionException(
                exception: $e,
            );
        }

        $data = $response->json();

        if (isset($data['success']) && $data['success'] === false) {
            throw new FourWhatsException(
                message: $data['reason'],
            );
        }

        return [
            'success' => $data['success'],
        ];
    }
}
