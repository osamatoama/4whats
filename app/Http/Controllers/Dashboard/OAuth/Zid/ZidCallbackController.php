<?php

namespace App\Http\Controllers\Dashboard\OAuth\Zid;

use App\Dto\StoreDto;
use App\Dto\TokenDto;
use App\Dto\UserDto;
use App\Dto\WhatsappAccountDto;
use App\Dto\WidgetDto;
use App\Enums\MessageTemplate;
use App\Enums\ProviderType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Services\OAuth\OAuthService;
use App\Services\Setting\SettingService;
use App\Services\Store\StoreService;
use App\Services\Template\TemplateService;
use App\Services\Token\TokenService;
use App\Services\WhatsappAccount\WhatsappAccountService;
use App\Services\Widget\WidgetService;
use App\Services\Zid\OAuth\ZidOAuthException;
use App\Services\Zid\OAuth\ZidOAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ZidCallbackController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        try {
            $zidOAuthService = new ZidOAuthService();
            $token = $zidOAuthService->getToken(
                code: $request->query(
                    key: 'code',
                ),
            );

            $resourceOwner = $zidOAuthService->getResourceOwner(
                managerToken: $token->managerToken,
                accessToken: $token->accessToken,
            );

            $store = Store::query()->zid(providerId: $resourceOwner->store->id)->first();
            if ($store !== null) {
                (new TokenService())->syncToken(
                    user: $store->user,
                    tokenDto: TokenDto::fromZid(
                        zidToken: $token,
                    ),
                );

                auth()->guard(name: 'dashboard')->login(user: $store->user);

                return to_route(route: 'dashboard.home');
            }

            $oauthService = new OAuthService();
            $password = $oauthService->generatePassword();
            $user = $oauthService->getOrCreateUser(
                userDto: UserDto::fromZid(
                    zidUser: $resourceOwner,
                    password: $password,
                ),
                role: UserRole::MERCHANT,
                password: $password,
            );

            $store = DB::transaction(
                callback: function () use ($user, $token, $resourceOwner): Store {
                    (new TokenService())->syncToken(
                        user: $user,
                        tokenDto: TokenDto::fromZid(
                            zidToken: $token,
                        ),
                    );

                    $store = (new StoreService())->create(
                        storeDto: StoreDto::fromZid(
                            userId: $user->id,
                            zidStore: $resourceOwner->store,
                        ),
                    );

                    (new WidgetService())->create(
                        widgetDto: WidgetDto::fromDefault(
                            storeId: $store->id,
                        ),
                    );

                    (new TemplateService())->bulkCreate(
                        storeId: $store->id,
                        messageTemplates: MessageTemplate::zidCases()->toArray(),
                    );

                    (new SettingService())->createDefaultSettings(
                        storeId: $store->id,
                        providerType: ProviderType::ZID,
                    );

                    (new WhatsappAccountService())->create(
                        whatsappAccountDto: WhatsappAccountDto::fromExpired(
                            storeId: $store->id,
                            label: $store->name,
                        ),
                    );

                    auth()->guard(name: 'dashboard')->login(user: $user);

                    return $store;
                },
            );

            // TODO:pull zid data

            return to_route(route: 'dashboard.home');
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
