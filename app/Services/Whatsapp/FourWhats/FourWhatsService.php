<?php

namespace App\Services\Whatsapp\FourWhats;

use App\Services\Whatsapp\FourWhats\Support\Instance;
use App\Services\Whatsapp\FourWhats\Support\Instances;
use App\Services\Whatsapp\FourWhats\Support\Sending;
use App\Services\Whatsapp\FourWhats\Support\User;

readonly class FourWhatsService
{
    public Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function user(): User
    {
        return resolveSingletonIf(
            abstract: User::class,
            concrete: fn (): User => new User(service: $this, client: $this->client),
        );
    }

    public function instances(string $apiKey): Instances
    {
        return resolveSingletonIf(
            abstract: Instances::class.':'.$apiKey,
            concrete: fn (): Instances => new Instances(service: $this, client: $this->client, apiKey: $apiKey),
        );
    }

    public function instance(int $instanceId, string $instanceToken): Instance
    {
        return resolveSingletonIf(
            abstract: Instance::class.':'.$instanceId.':'.$instanceToken,
            concrete: fn (): Instance => new Instance(service: $this, client: $this->client, instanceId: $instanceId, instanceToken: $instanceToken),
        );
    }

    public function sending(int $instanceId, string $instanceToken): Sending
    {
        return resolveSingletonIf(
            abstract: Sending::class.':'.$instanceId.':'.$instanceToken,
            concrete: fn (): Sending => new Sending(service: $this, client: $this->client, instanceId: $instanceId, instanceToken: $instanceToken),
        );
    }
}
