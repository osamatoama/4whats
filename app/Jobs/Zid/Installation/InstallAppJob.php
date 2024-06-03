<?php

namespace App\Jobs\Zid\Installation;

use App\Dto\StoreDto;
use App\Dto\TokenDto;
use App\Dto\UserDto;
use App\Dto\WhatsappAccountDto;
use App\Dto\WidgetDto;
use App\Enums\MessageTemplate;
use App\Enums\ProviderType;
use App\Enums\UserRole;
use App\Jobs\Concerns\InteractsWithBatches;
use App\Models\Store;
use App\Services\OAuth\OAuthService;
use App\Services\Setting\SettingService;
use App\Services\Store\StoreService;
use App\Services\Template\TemplateService;
use App\Services\Token\TokenService;
use App\Services\User\UserService;
use App\Services\WhatsappAccount\WhatsappAccountService;
use App\Services\Widget\WidgetService;
use App\Services\Zid\OAuth\Support\Token;
use App\Services\Zid\OAuth\Support\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class InstallAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithBatches, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly User $zidUser,
        public readonly Token $zidToken,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $oauthService = new OAuthService();
        $password = $oauthService->generatePassword();
        $user = $oauthService->getOrCreateUser(
            userDto: UserDto::fromZid(
                zidUser: $this->zidUser,
                password: $password,
            ),
            role: UserRole::MERCHANT,
            password: $password,
        );

        $store = DB::transaction(
            callback: function () use ($user): Store {
                (new TokenService())->syncToken(
                    user: $user,
                    tokenDto: TokenDto::fromZid(
                        zidToken: $this->zidToken,
                    ),
                );

                $store = (new StoreService())->create(
                    storeDto: StoreDto::fromZid(
                        userId: $user->id,
                        zidStore: $this->zidUser->store,
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

                return $store;
            },
        );

        // TODO:pull data
        // TODO:register webhooks

        (new UserService())->sendCredentials(
            user: $user,
            password: $password,
        );
    }
}
