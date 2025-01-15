<?php

namespace App\Services\User;

use App\Dto\UserDto;
use App\Enums\Jobs\QueueName;
use App\Enums\UserRole;
use App\Jobs\FourWhats\FourWhatsCreateUserJob;
use App\Models\User;
use App\Notifications\User\SendCredentialsToUser;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function create(UserDto $userDto, UserRole $role, bool $createFourWhatsUser = true): User
    {
        $user = DB::transaction(
            callback: function () use ($userDto, $role): User {
                $user = User::query()
                    ->create(
                        attributes: [
                            'name' => $userDto->name,
                            'email' => $userDto->email,
                            'password' => $userDto->password,
                        ],
                    );

                $user->assignRole(
                    roles: $role->asModel(),
                );

                return $user;
            }
        );

        if ($createFourWhatsUser) {
            FourWhatsCreateUserJob::dispatch(
                user: $user,
                mobile: $userDto->mobile,
                password: $userDto->password,
            )->onQueue(QueueName::SUBSCRIPTIONS->value);
        }

        return $user;
    }

    public function sendCredentials(User $user, string $password, string $notificationClassName = SendCredentialsToUser::class): void
    {
        $user->notify(
            instance: new $notificationClassName(
                email: $user->email,
                password: $password,
            ),
        );
    }
}
