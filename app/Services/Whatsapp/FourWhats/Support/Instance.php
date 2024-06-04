<?php

namespace App\Services\Whatsapp\FourWhats\Support;

use App\Enums\Whatsapp\InstanceStatus;
use App\Enums\Whatsapp\QrCodeStatus;
use App\Services\Whatsapp\FourWhats\Client;
use App\Services\Whatsapp\FourWhats\Contracts\Support\Instance as InstanceContract;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use Illuminate\Http\Client\ConnectionException;

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
        try {
            $response = $this->client->get(
                url: $this->baseUrl.'/qr_code',
                data: [
                    'instanceid' => $this->instanceId,
                    'token' => $this->instanceToken,
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
            'status' => $data['accountStatus'] === 'got qr code' ? QrCodeStatus::GOT_QR_CODE : QrCodeStatus::AUTHENTICATED,
            'qr_code' => $data['qrCode'],
        ];
    }

    public function info(): array
    {
        try {
            $response = $this->client->get(
                url: $this->baseUrl.'/me',
                data: [
                    'instanceid' => $this->instanceId,
                    'token' => $this->instanceToken,
                ],
            );
        } catch (ConnectionException $e) {
            throw FourWhatsException::connectionException(
                exception: $e,
            );
        }

        $data = $response->json();

        if ($data === null) {
            throw new FourWhatsException();
        }

        if (isset($data['success']) && $data['success'] === false) {
            return [
                'status' => InstanceStatus::DISCONNECTED,
                'wid' => null,
                'mobile' => null,
            ];
        }

        return [
            'status' => InstanceStatus::CONNECTED,
            'wid' => $data['info']['wid']['_serialized'],
            'mobile' => '+'.$data['info']['wid']['user'],
        ];
    }

    /**
     * @throws FourWhatsException
     */
    public function logout(): array
    {
        try {
            $response = $this->client->get(
                url: $this->baseUrl.'/logout',
                data: [
                    'instanceid' => $this->instanceId,
                    'token' => $this->instanceToken,
                ],
            );
        } catch (ConnectionException $e) {
            throw FourWhatsException::connectionException(
                exception: $e,
            );
        }

        $data = $response->json();

        if (isset($data['success']) && $data['success'] === false) {
            throw new FourWhatsException(message: $data['reason']);
        }

        return [
            'logged_out' => $data['success'],
        ];
    }
}
