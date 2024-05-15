<?php

namespace App\Services\Whatsapp\FourWhats;

use App\Services\Whatsapp\FourWhats\Contracts\Support\Instance as InstanceContract;
use App\Services\Whatsapp\FourWhats\Contracts\Support\Instances as InstancesContract;
use App\Services\Whatsapp\FourWhats\Contracts\Support\Sending as SendingContract;
use App\Services\Whatsapp\FourWhats\Contracts\Support\User as UserContract;
use App\Services\Whatsapp\FourWhats\Fakes\Support\Instances as FakeInstances;
use App\Services\Whatsapp\FourWhats\Fakes\Support\User as FakeUser;
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

    public function user(): UserContract
    {
        return resolveSingletonIf(
            abstract: UserContract::class,
            concrete: fn (): UserContract => new (app()->isProduction() ? User::class : FakeUser::class)(service: $this, client: $this->client),
        );
    }

    public function instances(string $apiKey): InstancesContract
    {
        return resolveSingletonIf(
            abstract: InstancesContract::class.':'.$apiKey,
            concrete: fn (): InstancesContract => new (app()->isProduction() ? Instances::class : FakeInstances::class)(service: $this, client: $this->client, apiKey: $apiKey),
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
}
