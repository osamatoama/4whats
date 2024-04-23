<?php

namespace App\Jobs\Salla\Webhook;

use App\Enums\ProviderType;
use App\Enums\UserRole;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            $generatedPassword = null;
            $user = DB::transaction(callback: function () use ($resourceOwner, $email, &$generatedPassword): User {
                $user = User::where(column: 'email', operator: '=', value: $email)->first();
                if ($user === null) {
                    $generatedPassword = Str::password();

                    $user = User::query()->create(attributes: [
                        'name' => $resourceOwner->getName(),
                        'email' => $email,
                        'password' => $generatedPassword,
                    ]);

                    $user->assignRole(UserRole::MERCHANT);
                }

                return $user;
            });

            if ($generatedPassword !== null) {
                $user->notify(instance: new UserCreatedUsingSallaWebhook(
                    email: $email,
                    password: $generatedPassword,
                ));
            }

            DB::transaction(callback: function () use ($resourceOwner, $user): void {
                $user->tokens()->create(attributes: [
                    'provider_type' => ProviderType::SALLA,
                    'access_token' => $this->data['access_token'],
                    'refresh_token' => $this->data['refresh_token'],
                    'expired_at' => $this->data['expires'],
                ]);

                $data = $resourceOwner->toArray();
                $user->store()->create(attributes: [
                    'provider_type' => ProviderType::SALLA,
                    'provider_id' => $data['merchant']['id'],
                    'name' => $data['merchant']['name'],
                    'mobile' => $data['mobile'],
                    'email' => $data['email'],
                    'domain' => $data['merchant']['domain'],
                ]);
            });
        } catch (Exception $e) {
            logger()->error(message: $e);
        }
    }
}
