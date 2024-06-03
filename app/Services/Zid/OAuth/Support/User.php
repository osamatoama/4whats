<?php

namespace App\Services\Zid\OAuth\Support;

final readonly class User
{
    public function __construct(
        public string $name,
        public string $email,
        public string $mobile,
        public Store $store,
    ) {
    }
}
