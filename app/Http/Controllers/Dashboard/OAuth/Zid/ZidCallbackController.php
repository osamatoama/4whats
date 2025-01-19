<?php

namespace App\Http\Controllers\Dashboard\OAuth\Zid;

use App\Dto\TokenDto;
use App\Enums\Jobs\BatchName;
use App\Enums\Jobs\QueueName;
use App\Http\Controllers\Controller;
use App\Jobs\Zid\Installation\InstallAppJob;
use App\Models\Store;
use App\Services\Queue\BatchService;
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

            logger()->error(
                message: 'logger 1',
                context: [
                    'zidToken' => $zidToken,
                ],
            );

            $resourceOwner = $zidOAuthService->getResourceOwner(
                managerToken: $zidToken->managerToken,
                accessToken: $zidToken->accessToken,
            );

            logger()->error(
                message: 'logger 2',
                context: [
                    'resourceOwner' => $resourceOwner,
                ],
            );

            $store = Store::query()->zid(providerId: $resourceOwner->store->id)->first();

            logger()->error(
                message: 'logger 3',
                context: [
                    'store' => $$store,
                ],
            );


            if ($store !== null) {
                logger()->error(
                    message: 'before syncToken',
                
                );
                (new TokenService())->syncToken(
                    user: $store->user,
                    tokenDto: TokenDto::fromZid(
                        zidToken: $zidToken,
                    ),
                );

                logger()->error(
                    message: 'after syncToken',
                    context: [
                        'storeUser' => $store->user,
                    ],
                );

                auth()->guard(
                    name: 'dashboard',
                )->login(
                    user: $store->user,
                );

                return to_route(
                    route: 'dashboard.home',
                );
            }

            logger()->error(
                message: 'before batch',
                context: [
                    'batch' => BatchService::doesntHaveRunningBatches(
                        batchName: BatchName::ZID_INSTALLATION,
                        storeId: $resourceOwner->store->id,
                    ),
                ],
            );

            if (BatchService::doesntHaveRunningBatches(
                batchName: BatchName::ZID_INSTALLATION,
                storeId: $resourceOwner->store->id,
            )) {
                logger()->error(
                    message: 'before install app batch',
                );

                BatchService::createPendingBatch(
                    jobs: new InstallAppJob(
                        zidUser: $resourceOwner,
                        zidToken: $zidToken,
                    ),
                    batchName: BatchName::ZID_INSTALLATION,
                    storeId: $resourceOwner->store->id,
                    deleteWhenFinished: true,
                    queue: QueueName::SUBSCRIPTIONS->value,
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

            return to_route(
                route: 'dashboard.login',
            )->with(
                key: 'error',
                value: __(
                    key: 'dashboard.common.something_went_wrong',
                ),
            );
        }
    }
}
