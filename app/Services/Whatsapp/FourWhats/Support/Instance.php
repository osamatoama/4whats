<?php

namespace App\Services\Whatsapp\FourWhats\Support;

use App\Enums\Whatsapp\QrCodeStatus;
use App\Services\Whatsapp\FourWhats\Client;
use App\Services\Whatsapp\FourWhats\Contracts\Support\Instance as InstanceContract;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;

class Instance implements InstanceContract
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
    public function qrCode(): array
    {
        $response = $this->client->get(
            url: $this->baseUrl.'/qr_code',
            data: [
                'instanceid' => $this->instanceId,
                'token' => $this->instanceToken,
            ],
        );

        $data = $response->json();

        if (isset($data['success']) && $data['success'] === false) {
            throw new FourWhatsException(message: $data['reason']);
        }

        return [
            'status' => $data['accountStatus'] === 'got qr code' ? QrCodeStatus::GOT_QR_CODE : QrCodeStatus::AUTHENTICATED,
            'qr_code' => $data['qrCode'],
        ];
    }

    /**
     * @throws FourWhatsException
     */
    public function logout(): array
    {
        $response = $this->client->get(
            url: $this->baseUrl.'/logout',
            data: [
                'instanceid' => $this->instanceId,
                'token' => $this->instanceToken,
            ],
        );

        $data = $response->json();

        if (isset($data['success']) && $data['success'] === false) {
            throw new FourWhatsException(message: $data['reason']);
        }

        return [
            'logged_out' => $data['success'],
        ];
    }
}
