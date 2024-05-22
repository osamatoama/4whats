<?php

namespace App\Services\Whatsapp\FourWhats;

use App\Services\Whatsapp\FourWhats\Contracts\Support\Instance as InstanceContract;
use App\Services\Whatsapp\FourWhats\Contracts\Support\Instances as InstancesContract;
use App\Services\Whatsapp\FourWhats\Contracts\Support\Sending as SendingContract;
use App\Services\Whatsapp\FourWhats\Contracts\Support\User as UserContract;
use App\Services\Whatsapp\FourWhats\Contracts\Support\Webhook as WebhookContract;
use App\Services\Whatsapp\FourWhats\Fakes\Support\Instances as FakeInstances;
use App\Services\Whatsapp\FourWhats\Fakes\Support\User as FakeUser;
use App\Services\Whatsapp\FourWhats\Support\Instance;
use App\Services\Whatsapp\FourWhats\Support\Instances;
use App\Services\Whatsapp\FourWhats\Support\Sending;
use App\Services\Whatsapp\FourWhats\Support\User;
use App\Services\Whatsapp\FourWhats\Support\Webhook;

readonly class FourWhatsService
{
    public Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function testMode(): bool
    {
        return config(key: 'services.four_whats.test_mode');
    }

    public function user(): UserContract
    {
        return resolveSingletonIf(
            abstract: UserContract::class,
            concrete: fn (): UserContract => new ($this->testMode() ? FakeUser::class : User::class)(service: $this, client: $this->client),
        );
    }

    public function instances(string $apiKey): InstancesContract
    {
        return resolveSingletonIf(
            abstract: InstancesContract::class.':'.$apiKey,
            concrete: fn (): InstancesContract => new ($this->testMode() ? FakeInstances::class : Instances::class)(service: $this, client: $this->client, apiKey: $apiKey),
        );
    }

    public function instance(int $instanceId, string $instanceToken): InstanceContract
    {
        return resolveSingletonIf(
            abstract: InstanceContract::class.':'.$instanceId.':'.$instanceToken,
            concrete: fn (): InstanceContract => new Instance(service: $this, client: $this->client, instanceId: $instanceId, instanceToken: $instanceToken),
        );
    }

    public function sending(int $instanceId, string $instanceToken): SendingContract
    {
        return resolveSingletonIf(
            abstract: SendingContract::class.':'.$instanceId.':'.$instanceToken,
            concrete: fn (): SendingContract => new Sending(service: $this, client: $this->client, instanceId: $instanceId, instanceToken: $instanceToken),
        );
    }

    public function webhook(int $instanceId, string $instanceToken): WebhookContract
    {
        return resolveSingletonIf(
            abstract: WebhookContract::class.':'.$instanceId.':'.$instanceToken,
            concrete: fn (): WebhookContract => new Webhook(service: $this, client: $this->client, instanceId: $instanceId, instanceToken: $instanceToken),
        );
    }
}
