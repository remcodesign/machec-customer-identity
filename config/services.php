<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
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

    // The other two co-located admin apps' base URLs, used only by
    // <x-machec::admin-nav>'s switcher links (D20) — a plain <a href> to
    // each app's own /login page, never a session handoff.
    'commercial_core' => [
        'url' => env('COMMERCIAL_CORE_URL', 'https://machec-commercial-core.ddev.site'),
    ],

    'logistics_wms' => [
        'url' => env('LOGISTICS_WMS_URL', 'https://machec-logistics-wms.ddev.site'),
    ],

    // Fixed password for the seeded customer_admin demo account
    // (DatabaseSeeder) — known only to whoever holds this app's real .env,
    // so the same seeded account can be logged into from ddev or a deployed
    // environment without a random per-run password. Override per
    // environment for a stronger deployed secret.
    // .env variable: DEMO_ADMIN_PASSWORD can not be empty!
    'demo' => [
        'admin_password' => env('ADMIN_PASSWORD'),
    ],

    // Whether a password-reset request (D79) also sends the requesting
    // customer a plain acknowledgement email, on top of the admin
    // notification that always goes out. No token/link, since there is no
    // self-service reset flow yet.
    'notify_customer_on_password_request' => env('NOTIFY_CUSTOMER_ON_PASSWORD_REQUEST', false),

];
