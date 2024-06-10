<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'salla' => [
        'app_id' => env(key: 'SALLA_APP_ID'),
        'client_id' => env(key: 'SALLA_CLIENT_ID'),
        'client_secret' => env(key: 'SALLA_CLIENT_SECRET'),
        'webhook_token' => env(key: 'SALLA_WEBHOOK_TOKEN'),
    ],

    'zid' => [
        'app_id' => env(key: 'ZID_APP_ID'),
        'client_id' => env(key: 'ZID_CLIENT_ID'),
        'client_secret' => env(key: 'ZID_CLIENT_SECRET'),
        'webhook_token' => env(key: 'ZID_WEBHOOK_TOKEN'),
    ],

    'four_whats' => [
        'test_mode' => (bool) env(key: 'FOUR_WHATS_TEST_MODE', default: true),
        'voucher' => env(key: 'FOUR_WHATS_VOUCHER'),
        'auth_key' => env(key: 'FOUR_WHATS_AUTH_KEY'),
        'support' => [
            'instance_id' => env(key: 'FOUR_WHATS_SUPPORT_INSTANCE_ID'),
            'instance_token' => env(key: 'FOUR_WHATS_SUPPORT_INSTANCE_TOKEN'),
            'email' => env(key: 'FOUR_WHATS_SUPPORT_EMAIL'),
            'mobile' => env(key: 'FOUR_WHATS_SUPPORT_MOBILE'),
        ],
    ],

];
