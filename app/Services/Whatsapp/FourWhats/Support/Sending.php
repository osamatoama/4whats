<?php

namespace App\Services\Whatsapp\FourWhats\Support;

use App\Services\Whatsapp\FourWhats\Client;
use App\Services\Whatsapp\FourWhats\Contracts\Support\Sending as SendingContract;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use Illuminate\Http\Client\ConnectionException;

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
        try {
            $response = $this->client->get(
                url: $this->baseUrl.'/sendMessage',
                data: [
                    'instanceid' => $this->instanceId,
                    'token' => $this->instanceToken,
                    'phone' => $mobile,
                    'body' => $message,
                ],
            );
        } catch (ConnectionException $e) {
            logger()->error(
                message: 'Send whatsapp message catch block logger',
                context: [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'trace' => $e->getTraceAsString(),
                    'line' => $e->getLine(),
                ],
            );
            throw FourWhatsException::connectionException(
                exception: $e,
            );
        }

        $data = $response->json();

        logger()->error(
            message: 'Send whatsapp message logger',
            context: $data,
        );

        if (isset($data['success']) && $data['success'] === false) {
            throw new FourWhatsException(
                message: $data['reason'] ?? '',
            );
        }

        return [
            'id' => $data['id'],
        ];
    }

    /**
     * @throws FourWhatsException
     */
    public function file(string $mobile, string $fileName, string $fileUrl, ?string $caption = null): array
    {
        try {
            $response = $this->client->get(
                url: $this->baseUrl.'/sendFile',
                data: [
                    'instanceid' => $this->instanceId,
                    'token' => $this->instanceToken,
                    'phone' => $mobile,
                    'body' => $fileUrl,
                    'filename' => $fileName,
                    'caption' => $caption,
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

        if (isset($data['sent']) && $data['sent'] === false) {
            throw new FourWhatsException(
                message: $data['reason'],
            );
        }

        return [
            'id' => $data['id'],
        ];
    }

    /**
     * @throws FourWhatsException
     */
    public function ppt(string $mobile, string $fileUrl): array
    {
        try {
            $response = $this->client->get(
                url: $this->baseUrl.'/sendPTT',
                data: [
                    'instanceid' => $this->instanceId,
                    'token' => $this->instanceToken,
                    'phone' => $mobile,
                    'audio' => $fileUrl,
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
            'id' => $data['id'],
        ];
    }
}
