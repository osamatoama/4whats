<?php

namespace App\Services\Whatsapp\FourWhats\Contracts\Support;

use App\Services\Whatsapp\FourWhats\FourWhatsException;

interface User
{
    /**
     * @throws FourWhatsException
     */
    public function create(string $name, string $email, string $mobile, string $password): array;

    /**
     * @throws FourWhatsException
     */
    public function findByEmail(string $email): array;

    /**
     * @throws FourWhatsException
     */
    public function findByMobile(string $mobile): array;
}
