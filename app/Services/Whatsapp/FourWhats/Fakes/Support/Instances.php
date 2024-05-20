<?php

namespace App\Services\Whatsapp\FourWhats\Fakes\Support;

use App\Services\Whatsapp\FourWhats\Client;
use App\Services\Whatsapp\FourWhats\Contracts\Support\Instances as InstancesContract;
use App\Services\Whatsapp\FourWhats\FourWhatsService;

class Instances implements InstancesContract
{
    protected string $baseUrl = 'https://api.4whats.net';

    public function __construct(
        protected FourWhatsService $service,
        protected Client $client,
        protected string $apiKey,
    ) {
    }

    public function create(string $email, int $packageId): array
    {
        return [
            'instance_id' => '135940',
            'instance_token' => '58999521-f86e-4bdf-8053-0f7f52604d77',
        ];
    }

    public function renew(string $email, int $instanceId, int $packageId): array
    {
        return [
            'instance_id' => '135940',
        ];
    }
}
