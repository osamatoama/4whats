<?php

namespace App\Services\Whatsapp\FourWhats\Contracts\Support;

use App\Services\Whatsapp\FourWhats\FourWhatsException;

interface User
{
    /**
     * @throws FourWhatsException
     */
    public function create(string $name, string $email, string $mobile, string $password): array;
}
