<?php

namespace App\Http\Controllers\Dashboard\OAuth\Zid;

use App\Http\Controllers\Controller;
use App\Services\Zid\OAuth\ZidOAuthService;
use Illuminate\Http\RedirectResponse;

class ZidRedirectController extends Controller
{
    public function __invoke(ZidOAuthService $service): RedirectResponse
    {
        logger()->error(
            message: 'before redirect',
            context: [
                'url' => $service->getRedirectUrl(),
            ],
        );

        return redirect()->away(
            path: $service->getRedirectUrl(),
        );
    }
}
