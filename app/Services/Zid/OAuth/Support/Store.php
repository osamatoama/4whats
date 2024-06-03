<?php

namespace App\Services\Zid\OAuth\Support;

final readonly class Store
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public string $mobile,
        public string $url,
    ) {
    }
}
