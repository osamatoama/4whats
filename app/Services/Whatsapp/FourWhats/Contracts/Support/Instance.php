<?php

namespace App\Services\Whatsapp\FourWhats\Contracts\Support;

use App\Services\Whatsapp\FourWhats\FourWhatsException;

interface Instance
{
    /**
     * @throws FourWhatsException
     */
    public function qrCode(): array;

    /**
     * @throws FourWhatsException
     */
    public function info(): array;

    /**
     * @throws FourWhatsException
     */
    public function logout(): array;
}
