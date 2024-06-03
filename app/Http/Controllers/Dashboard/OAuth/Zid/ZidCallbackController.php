<?php

namespace App\Http\Controllers\Dashboard\OAuth\Zid;

use App\Dto\TokenDto;
use App\Enums\Jobs\BatchName;
use App\Http\Controllers\Controller;
use App\Jobs\Zid\Installation\InstallAppJob;
use App\Models\QueuedJobBatch;
use App\Models\Store;
use App\Services\Token\TokenService;
use App\Services\Zid\OAuth\ZidOAuthException;
use App\Services\Zid\OAuth\ZidOAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ZidCallbackController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        try {
            $zidOAuthService = new ZidOAuthService();
            $zidToken = $zidOAuthService->getToken(
                code: $request->query(
                    key: 'code',
                ),
            );

            $resourceOwner = $zidOAuthService->getResourceOwner(
                managerToken: $zidToken->managerToken,
                accessToken: $zidToken->accessToken,
            );

            $store = Store::query()->zid(providerId: $resourceOwner->store->id)->first();
            if ($store !== null) {
                (new TokenService())->syncToken(
                    user: $store->user,
                    tokenDto: TokenDto::fromZid(
                        zidToken: $zidToken,
                    ),
                );

                auth()->guard(name: 'dashboard')->login(user: $store->user);

                return to_route(route: 'dashboard.home');
            }

            if (QueuedJobBatch::doesntHaveRunningBatches(batchName: BatchName::ZID_INSTALLATION, storeId: $resourceOwner->store->id)) {
                QueuedJobBatch::createPendingBatch(
                    jobs: new InstallAppJob(
                        zidUser: $resourceOwner,
                        zidToken: $zidToken,
                    ),
                    batchName: BatchName::ZID_INSTALLATION,
                    storeId: $resourceOwner->store->id,
                    deleteWhenFinished: true,
                )->dispatch();
            }

            return to_route(route: 'dashboard.login')->with(
                key: 'success',
                value: __(
                    key: 'dashboard.messages.installing_app',
                ),
            );
        } catch (ZidOAuthException $e) {
            logger()->error(
                message: $e->getMessage(),
            );

            return to_route(route: 'dashboard.login')->with(
                key: 'error',
                value: __(
                    key: 'dashboard.common.something_went_wrong',
                ),
            );
        }
    }
}
