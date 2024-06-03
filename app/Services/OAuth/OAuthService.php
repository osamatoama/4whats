<?php

namespace App\Services\OAuth;

use App\Dto\UserDto;
use App\Enums\UserRole;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Support\Str;

class OAuthService
{
    public function generatePassword(): string
    {
        return Str::password();
    }

    public function getOrCreateUser(UserDto $userDto, UserRole $role, string $password, bool $createFourWhatsUser = true): User
    {
        $user = User::query()
            ->where(
                column: 'email',
                operator: '=',
                value: $userDto->email,
            )
            ->first();

        if ($user !== null) {
            return $user;
        }

        return (new UserService())->create(
            userDto: $userDto,
            role: $role,
            createFourWhatsUser: $createFourWhatsUser,
        );
    }
}
