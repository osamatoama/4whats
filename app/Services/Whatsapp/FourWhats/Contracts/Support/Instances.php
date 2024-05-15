<?php

namespace App\Services\Whatsapp\FourWhats\Contracts\Support;

use App\Services\Whatsapp\FourWhats\FourWhatsException;

interface Instances
{
    /**
     * @throws FourWhatsException
     */
    public function create(string $email, int $packageId): array;

    /**
     * @throws FourWhatsException
     */
    public function renew(string $email, int $instanceId, int $packageId): array;
}
