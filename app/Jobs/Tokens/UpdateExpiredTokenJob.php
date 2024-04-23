<?php

namespace App\Jobs\Tokens;

use App\Enums\ProviderType;
use App\Jobs\Concerns\HandleException;
use App\Models\Token;
use App\Services\Salla\OAuth\SallaOAuthException;
use App\Services\Salla\OAuth\SallaOAuthService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateExpiredTokenJob implements ShouldQueue
{
    use Dispatchable, HandleException, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Token $token,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        match ($this->token->provider_type) {
            ProviderType::SALLA => $this->handleSalla(),
        };
    }

    protected function handleSalla(): void
    {
        try {
            $token = (new SallaOAuthService())->getNewToken(refreshToken: $this->token->refresh_token);
        } catch (SallaOAuthException $e) {
            $this->handleException(
                e: new SallaOAuthException(message: "Exception while updating salla access token | Token ID: $this->token->id | Message: {$e->getMessage()}", code: $e->getCode()),
            );

            return;
        }

        $this->token->update(attributes: [
            'access_token' => $token->getToken(),
            'refresh_token' => $token->getRefreshToken(),
            'expired_at' => $token->getExpires(),
        ]);
    }
}
