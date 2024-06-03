<?php

namespace App\Services\Token;

use App\Dto\TokenDto;
use App\Enums\ProviderType;
use App\Models\Token;
use App\Models\User;
use App\Services\Salla\OAuth\SallaOAuthException;
use App\Services\Salla\OAuth\SallaOAuthService;
use App\Services\Zid\OAuth\ZidOAuthException;
use App\Services\Zid\OAuth\ZidOAuthService;

class TokenService
{
    /**
     * @throws SallaOAuthException
     * @throws ZidOAuthException
     */
    public function getNewAccessToken(Token $token): string
    {
        return match ($token->provider_type) {
            ProviderType::SALLA => $this->getNewAccessTokenForSalla(
                token: $token,
            ),
            ProviderType::ZID => $this->getNewAccessTokenForZid(
                token: $token,
            ),
        };
    }

    public function syncToken(User $user, TokenDto $tokenDto): void
    {
        $user->providerTokens()->updateOrCreate(
            attributes: [
                'provider_type' => $tokenDto->providerType,
            ],
            values: [
                'access_token' => $tokenDto->accessToken,
                'refresh_token' => $tokenDto->refreshToken,
                'expired_at' => $tokenDto->expiredAt,
                'manager_token' => $tokenDto->managerToken,
            ],
        );
    }

    /**
     * @throws SallaOAuthException
     */
    protected function getNewAccessTokenForSalla(Token $token): string
    {
        $sallaToken = (new SallaOAuthService())->getNewToken(
            refreshToken: $token->refresh_token,
        );

        $accessToken = $sallaToken->getToken();

        $token->update(attributes: [
            'access_token' => $accessToken,
            'refresh_token' => $sallaToken->getRefreshToken(),
            'expired_at' => $sallaToken->getExpires(),
        ]);

        return $accessToken;
    }

    /**
     * @throws ZidOAuthException
     */
    protected function getNewAccessTokenForZid(Token $token): string
    {
        $zidToken = (new ZidOAuthService())->getNewToken(
            refreshToken: $token->refresh_token,
        );

        $accessToken = $zidToken->accessToken;

        $token->update(attributes: [
            'access_token' => $accessToken,
            'refresh_token' => $zidToken->refreshToken,
            'expired_at' => $zidToken->expiresIn,
            'manager_token' => $zidToken->managerToken,
        ]);

        return $accessToken;
    }
}
