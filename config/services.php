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

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        // Caminho do arquivo de verificação de domínio do Apple Pay.
        // A Stripe gera o conteúdo em: Dashboard > Settings > Payment Methods > Apple Pay > "Add new domain".
        // Cole o conteúdo (sem extensão) no arquivo abaixo e o domínio será verificado.
        'apple_pay_domain_association_path' => env(
            'STRIPE_APPLE_PAY_DOMAIN_ASSOCIATION_PATH',
            storage_path('app/applepay/apple-developer-merchantid-domain-association')
        ),
    ],

    'mercadopago' => [
        'access_token' => env('MERCADO_PAGO_ACCESS_TOKEN'),
    ],

];
