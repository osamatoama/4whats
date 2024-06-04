<?php

namespace App\Services\Whatsapp\FourWhats\Support;

use App\Services\Whatsapp\FourWhats\Client;
use App\Services\Whatsapp\FourWhats\Contracts\Support\User as UserContract;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;
use Illuminate\Http\Client\ConnectionException;

class User implements UserContract
{
    protected string $baseUrl = 'https://api.4whats.net';

    public function __construct(
        protected FourWhatsService $service,
        protected Client $client,
    ) {
    }

    /**
     * @throws FourWhatsException
     */
    public function create(string $name, string $email, string $mobile, string $password): array
    {
        try {
            $response = $this->client->get(
                url: $this->baseUrl.'/createAppUser',
                data: [
                    'name' => $name,
                    'email' => $email,
                    'mobile' => $mobile,
                    'password' => $password,
                    'confirmpassword' => $password,
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
                message: $data['msg'],
            );
        }

        return [
            'id' => $data['user']['id'],
            'email' => $data['user']['email'],
            'mobile' => $data['user']['mobile'],
            'api_key' => $data['user']['apikey'],
        ];
    }
}
