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
    ],

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'url' => env('OPENAI_API_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'timeout' => env('OPENAI_TIMEOUT', 15),
    ],

    'weather' => [
        'key' => env('WEATHER_API_KEY'),
        'url' => env('WEATHER_API_URL', 'https://api.openweathermap.org/data/2.5'),
        'timeout' => env('WEATHER_TIMEOUT', 8),
    ],

    'cloudinary' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key' => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
        'timeout' => env('CLOUDINARY_TIMEOUT', 15),
    ],

    'visual_search' => [
        'key' => env('VISUAL_SEARCH_API_KEY'),
        'url' => env('VISUAL_SEARCH_API_URL'),
        'provider' => env('VISUAL_SEARCH_PROVIDER', 'external'),
        'timeout' => env('VISUAL_SEARCH_TIMEOUT', 15),
        'cache_minutes' => env('VISUAL_SEARCH_CACHE_MINUTES', 60),
    ],

];
