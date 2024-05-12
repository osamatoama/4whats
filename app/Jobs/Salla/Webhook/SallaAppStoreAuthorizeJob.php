<?php

namespace App\Jobs\Salla\Webhook;

use App\Enums\ProviderType;
use App\Enums\Settings\StoreSettings;
use App\Enums\UserRole;
use App\Jobs\FourWhats\FourWhatsCreateUserJob;
use App\Jobs\Salla\Pull\AbandonedCarts\SallaPullAbandonedCartsJob;
use App\Jobs\Salla\Pull\Customers\SallaPullCustomersJob;
use App\Jobs\Salla\Pull\OrderStatuses\SallaPullOrderStatusesJob;
use App\Models\Store;
use App\Models\User;
use App\Notifications\Salla\UserCreatedUsingSallaWebhook;
use App\Services\Salla\OAuth\SallaOAuthService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Salla\OAuth2\Client\Provider\SallaUser;

class SallaAppStoreAuthorizeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $merchantId,
        public array $data,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (Store::query()->salla(providerId: $this->merchantId)->exists()) {
            return;
        }

        try {
            $resourceOwner = (new SallaOAuthService())->getResourceOwner(accessToken: $this->data['access_token']);
            $email = $resourceOwner->getEmail();

            $user = User::where(column: 'email', operator: '=', value: $email)->firstOr(
                callback: fn (): User => $this->createUser(resourceOwner: $resourceOwner, email: $email),
            );

            $store = DB::transaction(callback: function () use ($resourceOwner, $user): Store {
                $this->createToken(user: $user);

                $store = $this->createStore(user: $user, resourceOwner: $resourceOwner);

                $this->createWidget(store: $store);
                $this->createSettings(store: $store);
                $this->createExpiredWhatsappAccount(store: $store);

                return $store;
            });

            $this->pullStoreData(store: $store);
        } catch (Exception $e) {
            logger()->error(message: $e);
        }
    }

    protected function createUser(SallaUser $resourceOwner, string $email): User
    {
        $userData = DB::transaction(callback: function () use ($resourceOwner, $email): array {
            $password = Str::password();

            $user = User::query()->create(attributes: [
                'name' => $resourceOwner->getName(),
                'email' => $email,
                'password' => $password,
            ]);

            $user->assignRole(UserRole::MERCHANT);

            FourWhatsCreateUserJob::dispatch(user: $user, mobile: $resourceOwner->getMobile(), password: $password);

            return [
                'user' => $user,
                'password' => $password,
            ];
        });

        $user = $userData['user'];

        $user->notify(instance: new UserCreatedUsingSallaWebhook(
            email: $email,
            password: $userData['password'],
        ));

        return $user;
    }

    protected function createToken(User $user): void
    {
        $user->tokens()->create(attributes: [
            'provider_type' => ProviderType::SALLA,
            'access_token' => $this->data['access_token'],
            'refresh_token' => $this->data['refresh_token'],
            'expired_at' => $this->data['expires'],
        ]);
    }

    /**
     * @throws Exception
     */
    protected function createStore(User $user, SallaUser $resourceOwner): Store
    {
        $data = $resourceOwner->toArray();

        return $user->stores()->create(attributes: [
            'provider_type' => ProviderType::SALLA,
            'provider_id' => $data['merchant']['id'],
            'name' => $data['merchant']['name'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'domain' => $data['merchant']['domain'],
        ]);
    }

    protected function createWidget(Store $store): void
    {
        $store->widget()->create();
    }

    protected function createSettings(Store $store): void
    {
        $store->settings()->create(attributes: ['key' => StoreSettings::MESSAGES_ABANDONED_CARTS->value])->template()->create(attributes: [
            'store_id' => $store->id,
            'message' => 'Abandoned Cart',
            'placeholders' => [
                '[CUSTOMER_NAME]',
                '[CART_AMOUNT]',
                '[CART_CURRENCY]',
                '[CHECKOUT_URL]',
            ],
            'delay_in_seconds' => 60 * 60 * 2,
        ]);

        $store->settings()->create(attributes: ['key' => StoreSettings::MESSAGES_OTP->value])->template()->create(attributes: [
            'store_id' => $store->id,
            'message' => 'OTP',
            'placeholders' => [
                '[CUSTOMER_NAME]',
                '[OTP]',
            ],
        ]);
    }

    protected function createExpiredWhatsappAccount(Store $store): void
    {
        $store->whatsappAccount()->create(attributes: [
            'label' => $store->name,
            'mobile' => $store->mobile,
            'expired_at' => now()->subSecond(),
        ]);
    }

    protected function pullStoreData(Store $store): void
    {
        Bus::chain(jobs: [
            Bus::batch(jobs: new SallaPullCustomersJob(accessToken: $this->data['access_token'], storeId: $store->id))->name(name: 'salla.pull.customers:'.$store->id),
            Bus::batch(jobs: new SallaPullAbandonedCartsJob(accessToken: $this->data['access_token'], storeId: $store->id))->name(name: 'salla.pull.abandoned-carts:'.$store->id),
            Bus::batch(jobs: new SallaPullOrderStatusesJob(accessToken: $this->data['access_token'], storeId: $store->id))->name(name: 'salla.pull.order-statuses:'.$store->id),
        ])->dispatch();
    }
}
