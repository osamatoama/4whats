<?php

namespace App\Services\Whatsapp\FourWhats\Contracts\Support;

use App\Services\Whatsapp\FourWhats\FourWhatsException;

interface Sending
{
    /**
     * @throws FourWhatsException
     */
    public function text(string $mobile, string $message): array;

    /**
     * @throws FourWhatsException
     */
    public function file(string $mobile, string $fileName, string $fileUrl, ?string $caption = null): array;

    /**
     * @throws FourWhatsException
     */
    public function ppt(string $mobile, string $fileUrl): array;
}
