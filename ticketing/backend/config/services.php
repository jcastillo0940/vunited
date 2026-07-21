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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'payments' => [
        'base_url' => env('PAYMENTS_INTERNAL_BASE_URL'),
        'internal_secret' => env('PAYMENTS_INTERNAL_SECRET'),
    ],

    'ticket_qr' => [
        'signing_key' => env('TICKET_QR_SIGNING_KEY'),
    ],

    'google_wallet' => [
        'issuer_id' => env('GOOGLE_WALLET_ISSUER_ID'),
        'service_account_json' => env('GOOGLE_WALLET_SERVICE_ACCOUNT_JSON'),
    ],

    'apple_wallet' => [
        'pass_type_id' => env('APPLE_WALLET_PASS_TYPE_ID'),
        'team_id' => env('APPLE_WALLET_TEAM_ID'),
        'cert_path' => env('APPLE_WALLET_CERT_PATH'),
    ],

];
