<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\Dashboard\Auth\ResetPasswordRequest;

class NewPasswordController extends Controller
{
    public function create(Request $request): View
    {
        return view(
            view: 'dashboard.pages.auth.reset-password',
            data: [
                'token' => $request->route(
                    param: 'token',
                ),
                'email' => $request->query(
                    key: 'email',
                ),
            ],
        );
    }

    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            credentials: $request->only(
                keys: ['email', 'password', 'password_confirmation', 'token'],
            ),
            callback: function (User $user) use ($request): void {
                $user->forceFill(
                    attributes: [
                        'password' => $request->validated(
                            key: 'password',
                        ),
                        // 'password' => Hash::make(
                        //     value: $request->validated(
                        //         key: 'password',
                        //     )
                        // ),
                    ],
                )
                ->setRememberToken(
                    value: Str::random(
                        length: 60,
                    ),
                );

                $user->save();

                event(
                    event: new PasswordReset(
                        user: $user,
                    ),
                );
            },
        );

        return $status == Password::PASSWORD_RESET
            ? to_route(
                route: 'dashboard.login',
            )->with(
                key: 'status',
                value: __($status),
            )
            : back()->withInput(
                $request->only(
                    keys: ['email'],
                )
            )->withErrors(
                provider: ['email' => __($status)],
            );
    }
}
