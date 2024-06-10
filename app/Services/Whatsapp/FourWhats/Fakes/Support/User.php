<?php

namespace App\Services\Whatsapp\FourWhats\Fakes\Support;

use App\Services\Whatsapp\FourWhats\Client;
use App\Services\Whatsapp\FourWhats\Contracts\Support\User as UserContract;
use App\Services\Whatsapp\FourWhats\FourWhatsService;

class User implements UserContract
{
    protected string $baseUrl = 'https://api.4whats.net';

    public function __construct(
        protected FourWhatsService $service,
        protected Client $client,
    ) {
    }

    public function create(string $name, string $email, string $mobile, string $password): array
    {
        return [
            'id' => 5929,
            'email' => 'tech@valantica.com',
            'mobile' => '966530820588',
            'api_key' => 'cd92747a-c704-447a-9063-a7674c85edd1',
        ];
    }

    public function findByEmail(string $email): array
    {
        return [
            'id' => 5929,
            'email' => 'tech@valantica.com',
            'mobile' => '966530820588',
            'api_key' => 'cd92747a-c704-447a-9063-a7674c85edd1',
        ];
    }

    public function findByMobile(string $mobile): array
    {
        return [
            'id' => 5929,
            'email' => 'tech@valantica.com',
            'mobile' => '966530820588',
            'api_key' => 'cd92747a-c704-447a-9063-a7674c85edd1',
        ];
    }
}
