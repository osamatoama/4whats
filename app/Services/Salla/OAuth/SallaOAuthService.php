<?php

namespace App\Services\Salla\OAuth;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Salla\OAuth2\Client\Provider\Salla;
use Salla\OAuth2\Client\Provider\SallaUser;

class SallaOAuthService
{
    protected Salla $provider;

    public function __construct()
    {
        $this->provider = new Salla(options: [
            'clientId' => config(key: 'services.salla.client_id'),
            'clientSecret' => config(key: 'services.salla.client_secret'),
            'redirectUri' => null,
        ]);
    }

    /**
     * @throws SallaOAuthException
     */
    public function getNewToken(string $refreshToken): AccessToken
    {
        try {
            return $this->provider->getAccessToken(
                grant: 'refresh_token',
                options: ['refresh_token' => $refreshToken],
            );
        } catch (IdentityProviderException $e) {
            throw new SallaOAuthException(
                message: $e->getMessage(),
                code: $e->getCode(),
            );
        }
    }

    public function getResourceOwner(AccessToken|string $accessToken): SallaUser
    {
        if (is_string(value: $accessToken)) {
            $accessToken = new AccessToken(options: [
                'access_token' => $accessToken,
            ]);
        }

        return $this->provider->getResourceOwner(token: $accessToken);
    }
}
