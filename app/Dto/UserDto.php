<?php

namespace App\Dto;

use App\Services\Zid\OAuth\Support\User as ZidUser;
use Salla\OAuth2\Client\Provider\SallaUser;

final readonly class UserDto
{
    public function __construct(
        public ?int $userId,
        public string $name,
        public string $email,
        public string $password,
        public ?string $mobile,
    ) {
    }

    public static function fromSalla(SallaUser $sallaUser, string $password): self
    {
        return new self(
            userId: null,
            name: $sallaUser->getName(),
            email: $sallaUser->getEmail(),
            password: $password,
            mobile: $sallaUser->getMobile(),
        );
    }

    public static function fromZid(ZidUser $zidUser, string $password): self
    {
        return new self(
            userId: null,
            name: $zidUser->name,
            email: $zidUser->email,
            password: $password,
            mobile: $zidUser->mobile,
        );
    }
}
