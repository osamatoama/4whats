<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view(
            view: 'dashboard.pages.auth.login',
        );
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(
            default: route(
                name: 'dashboard.home',
            ),
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard(
            name: 'dashboard',
        )->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return to_route(
            route: 'dashboard.login',
        );
    }
}
