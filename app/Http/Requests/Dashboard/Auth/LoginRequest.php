<?php

namespace App\Http\Requests\Dashboard\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = User::query()->canAccessDashboard()->where(column: 'email', operator: '=', value: $this->input('email'))->first();
        if ($user === null || ! Hash::check(value: $this->input(key: 'password'), hashedValue: $user->password)) {
            RateLimiter::hit(key: $this->throttleKey());

            throw ValidationException::withMessages(messages: [
                'email' => trans(key: 'auth.failed'),
            ]);
        }

        Auth::guard(name: 'dashboard')->login(user: $user, remember: $this->boolean(key: 'remember'));

        RateLimiter::clear(key: $this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts(key: $this->throttleKey(), maxAttempts: 5)) {
            return;
        }

        event(new Lockout(request: $this));

        $seconds = RateLimiter::availableIn(key: $this->throttleKey());

        throw ValidationException::withMessages(messages: [
            'email' => trans(key: 'auth.throttle', replace: [
                'seconds' => $seconds,
                'minutes' => ceil(num: $seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(string: Str::lower(value: $this->input(key: 'email')).'|'.$this->ip());
    }
}
