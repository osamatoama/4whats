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
        $this->provider = new Salla([
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
            return $this->provider->getAccessToken(grant: 'refresh_token', options: ['refresh_token' => $refreshToken]);
        } catch (IdentityProviderException $e) {
            throw new SallaOAuthException($e->getMessage());
        }
    }

    public function getResourceOwner(AccessToken|string $accessToken): SallaUser
    {
        if (is_string($accessToken)) {
            $accessToken = new AccessToken([
                'access_token' => $accessToken,
            ]);
        }

        return $this->provider->getResourceOwner($accessToken);
    }
}
