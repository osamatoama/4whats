<?php

namespace App\Services\Zid\Merchant;

use App\Services\Zid\ZidException;
use Illuminate\Http\Client\Response;

class ZidMerchantException extends ZidException
{
    public static function fromResponse(Response $response, array $data): static
    {
        $messageName = $data['message']['name'];
        $messageDescription = $data['message']['description'];

        return new static(
            message: ($messageName !== null ? $messageName . ' ' : '') . $messageDescription,
            code: $response->status(),
        );
    }
}
