<?php

namespace App\Services\Whatsapp\FourWhats\Contracts\Support;

use App\Services\Whatsapp\FourWhats\FourWhatsException;

interface Webhook
{
    /**
     * @throws FourWhatsException
     */
    public function set(string $url): array;
}
