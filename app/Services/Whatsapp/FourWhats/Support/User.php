<?php

namespace App\Services\Whatsapp\FourWhats\Support;

use App\Services\Whatsapp\FourWhats\Client;
use App\Services\Whatsapp\FourWhats\FourWhatsException;
use App\Services\Whatsapp\FourWhats\FourWhatsService;

class User
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
    public function create(string $name, string $email, string $mobile, string $password)
    {
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

        $data = $response->json();

        $this->client->validateResponse(data: $data);

        return $data;
    }
}
