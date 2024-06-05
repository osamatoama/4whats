<?php

namespace App\Services\Zid\OAuth;

use App\Services\Zid\OAuth\Support\Store;
use App\Services\Zid\OAuth\Support\Token;
use App\Services\Zid\OAuth\Support\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

final readonly class ZidOAuthService
{
    public function getRedirectUrl(): string
    {
        $queries = http_build_query(
            data: [
                'client_id' => config(
                    key: 'services.zid.client_id',
                ),
                'redirect_uri' => route(
                    name: 'dashboard.oauth.zid.callback',
                ),
                'response_type' => 'code',
            ],
        );

        return 'https://oauth.zid.sa/oauth/authorize?'.$queries;
    }

    /**
     * @throws ZidOAuthException
     */
    public function getToken(?string $code): Token
    {
        try {
            $response = Http::acceptJson()->asJson()->post(
                url: 'https://oauth.zid.sa/oauth/token',
                data: [
                    'grant_type' => 'authorization_code',
                    'client_id' => config(
                        key: 'services.zid.client_id',
                    ),
                    'client_secret' => config(
                        key: 'services.zid.client_secret',
                    ),
                    'redirect_uri' => route(
                        name: 'dashboard.oauth.zid.callback',
                    ),
                    'code' => $code,
                ],
            );
        } catch (ConnectionException $e) {
            throw new ZidOAuthException(
                message: $e->getMessage(),
                code: $e->getCode(),
            );
        }

        $data = $response->json();

        if ($response->failed()) {
            throw new ZidOAuthException(
                message: $data['message']['description'],
                code: $response->status(),
            );
        }

        return new Token(
            managerToken: $data['access_token'],
            accessToken: $data['authorization'],
            refreshToken: $data['refresh_token'],
            expiresIn: $data['expires_in'],
        );
    }

    /**
     * @throws ZidOAuthException
     */
    public function getNewToken(string $refreshToken): Token
    {
        try {
            $response = Http::acceptJson()->asJson()->post(
                url: 'https://oauth.zid.sa/oauth/token',
                data: [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'client_id' => config(
                        key: 'services.zid.client_id',
                    ),
                    'client_secret' => config(
                        key: 'services.zid.client_secret',
                    ),
                    'redirect_uri' => route(
                        name: 'dashboard.oauth.zid.callback',
                    ),
                ],
            );
        } catch (ConnectionException $e) {
            throw new ZidOAuthException(
                message: $e->getMessage(),
                code: $e->getCode(),
            );
        }

        $data = $response->json();

        if ($response->failed()) {
            throw new ZidOAuthException(
                message: $data['message']['description'],
                code: $response->status(),
            );
        }

        return new Token(
            managerToken: $data['access_token'],
            accessToken: $data['authorization'],
            refreshToken: $data['refresh_token'],
            expiresIn: $data['expires_in'],
        );
    }

    /**
     * @throws ZidOAuthException
     */
    public function getResourceOwner(string $managerToken, string $accessToken): User
    {
        try {
            $response = Http::withHeaders(
                headers: [
                    'Accept-Language' => app()->getLocale(),
                    'X-Manager-Token' => $managerToken,
                ],
            )->withToken(
                token: $accessToken,
            )->acceptJson()->asJson()->get(
                url: 'https://api.zid.sa/v1/managers/account/profile',
            );
        } catch (ConnectionException $e) {
            throw new ZidOAuthException(
                message: $e->getMessage(),
                code: $e->getCode(),
            );
        }

        $data = $response->json();

        if ($response->failed()) {
            throw new ZidOAuthException(
                message: $data['message']['name'].' '.$data['message']['description'],
                code: $response->status(),
            );
        }

        $userData = $data['user'];
        $storeData = $data['user']['store'];

        return new User(
            name: $userData['name'],
            email: $userData['email'],
            mobile: $userData['mobile'],
            store: new Store(
                id: $storeData['id'],
                name: $storeData['title'],
                email: $storeData['email'],
                mobile: $storeData['phone'],
                url: $storeData['url'],
            ),
        );
    }
}
