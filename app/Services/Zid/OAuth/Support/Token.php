<?php

namespace App\Services\Zid\OAuth\Support;

final readonly class Token
{
    public function __construct(
        public string $managerToken,
        public string $accessToken,
        public string $refreshToken,
        public int $expiresIn,
    ) {
    }
}
