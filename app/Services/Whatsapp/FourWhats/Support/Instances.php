<?php

namespace App\Services\Whatsapp\FourWhats\Support;

use App\Enums\SettingKey;
use App\Services\Whatsapp\FourWhats\Client;
use App\Services\Whatsapp\FourWhats\Contracts\Support\Instances as InstancesContract;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use Illuminate\Http\Client\ConnectionException;

class Instances implements InstancesContract
{
    protected string $baseUrl = 'https://api.4whats.net';

    public function __construct(
        protected FourWhatsService $service,
        protected Client $client,
        protected string $apiKey,
    ) {
    }

    /**
     * @throws FourWhatsException
     */
    public function create(string $email, int $packageId): array
    {
        try {
            $response = $this->client->get(
                url: $this->baseUrl.'/newInstance',
                data: [
                    'email' => $email,
                    'apikey' => $this->apiKey,
                    'packageid' => $packageId,
                    'voucher' => settings()->value(key: SettingKey::SYSTEM_FOUR_WHATS_VOUCHER),
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
                errorCode: $data['errorCode'],
            );
        }

        return [
            'instance_id' => $data['instanceid'],
            'instance_token' => $data['token'],
        ];
    }

    /**
     * @throws FourWhatsException
     */
    public function renew(string $email, int $instanceId, int $packageId): array
    {
        try {
            $response = $this->client->get(
                url: $this->baseUrl.'/renewInstance',
                data: [
                    'email' => $email,
                    'apikey' => $this->apiKey,
                    'instanceid' => $instanceId,
                    'packageid' => $packageId,
                    'voucher' => settings()->value(key: SettingKey::SYSTEM_FOUR_WHATS_VOUCHER),
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
                errorCode: $data['errorCode'],
            );
        }

        return [
            'instance_id' => $data['instanceid'],
        ];
    }
}
