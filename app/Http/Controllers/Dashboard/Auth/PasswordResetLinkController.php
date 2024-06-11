<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Auth\ForgotPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view(
            view: 'dashboard.pages.auth.forgot-password',
        );
    }

    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        ResetPassword::createUrlUsing(
            callback: fn (User $user, string $token): string => url(
                path: route(
                    name: 'dashboard.password.reset',
                    parameters: [
                        'token' => $token,
                        'email' => $user->getEmailForPasswordReset(),
                    ],
                    absolute: false,
                ),
            ),
        );

        $status = Password::sendResetLink(
            credentials: $request->only(
                keys: ['email'],
            ),
        );

        return $status == Password::RESET_LINK_SENT
            ? back()->with(
                key: 'status',
                value: __(
                    key: $status,
                ),
            )
            : back()->withInput(
                input: $request->only(
                    keys: ['email'],
                )
            )->withErrors(
                provider: ['email' => __(
                    key: $status,
                )],
            );
    }
}
