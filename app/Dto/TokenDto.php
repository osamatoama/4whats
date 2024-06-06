<?php

namespace App\Dto;

use App\Enums\ProviderType;
use App\Services\Zid\OAuth\Support\Token as ZidToken;
use Illuminate\Support\Carbon;

final readonly class TokenDto
{
    public function __construct(
        public ProviderType $providerType,
        public string $accessToken,
        public string $refreshToken,
        public Carbon $expiredAt,
        public ?string $managerToken,
    ) {
    }

    public static function fromSalla(array $sallaToken): self
    {
        return new self(
            providerType: ProviderType::SALLA,
            accessToken: $sallaToken['access_token'],
            refreshToken: $sallaToken['refresh_token'],
            expiredAt: Carbon::parse(
                time: $sallaToken['expires'],
            ),
            managerToken: null,
        );
    }

    public static function fromZid(ZidToken $zidToken): self
    {
        return new self(
            providerType: ProviderType::ZID,
            accessToken: $zidToken->accessToken,
            refreshToken: $zidToken->refreshToken,
            expiredAt: now()->addSeconds(
                value: $zidToken->expiresIn,
            ),
            managerToken: $zidToken->managerToken,
        );
    }
}
