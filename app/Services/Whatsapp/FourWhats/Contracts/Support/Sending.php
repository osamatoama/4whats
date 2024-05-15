<?php

namespace App\Services\Whatsapp\FourWhats\Contracts\Support;

use App\Services\Whatsapp\FourWhats\FourWhatsException;

interface Sending
{
    /**
     * @throws FourWhatsException
     */
    public function text(string $mobile, string $message): array;
}
