<?php

namespace App\Services\Zid\Merchant;

use App\Services\Zid\ZidException;
use Illuminate\Http\Client\Response;

class ZidMerchantException extends ZidException
{
    public static function fromResponse(Response $response, array $data): static
    {
        return new static(
            message: $data['message']['description'],
            code: $response->status(),
        );
    }
}
