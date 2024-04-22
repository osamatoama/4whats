<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(Request $request): View
    {
        return view(view: 'dashboard.pages.auth.reset-password', data: [
            'token' => $request->route(param: 'token'),
            'email' => $request->query(key: 'email'),
        ]);
    }

    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            credentials: $request->only(keys: ['email', 'password', 'password_confirmation', 'token']),
            callback: function (User $user) use ($request): void {
                $user->forceFill(attributes: [
                    'password' => $request->validated(key: 'password'),
                    'remember_token' => Str::random(length: 60),
                ])->save();

                event(new PasswordReset(user: $user));
            },
        );

        return $status == Password::PASSWORD_RESET
            ? to_route(route: 'dashboard.login')->with(key: 'status', value: __($status))
            : back()->withInput($request->only(keys: ['email']))->withErrors(provider: ['email' => __($status)]);
    }
}
