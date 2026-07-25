<?php

return [
    'demo_mode' => (bool) env('VELORA_DEMO_MODE', false),
    'release' => [
        'version' => env('VELORA_RELEASE_VERSION', '1.0.0-rc.1'),
        'codename' => 'Release Candidate',
    ],
    'security' => [
        'csp' => env('VELORA_CSP', "default-src 'self'; img-src 'self' data: https:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src 'self'; connect-src 'self' ".env('FRONTEND_URL', 'http://localhost:5173').' '.env('APP_URL', 'http://localhost:8000').'; frame-ancestors \'none\'; base-uri \'self\'; form-action \'self\''),
        'hsts_max_age' => (int) env('VELORA_HSTS_MAX_AGE', 31536000),
        'trusted_proxies' => env('VELORA_TRUSTED_PROXIES', '*'),
        'password_history_count' => (int) env('VELORA_PASSWORD_HISTORY_COUNT', 5),
        'max_concurrent_sessions' => (int) env('VELORA_MAX_CONCURRENT_SESSIONS', 5),
        'suspicious_login_threshold' => (int) env('VELORA_SUSPICIOUS_LOGIN_THRESHOLD', 5),
        'upload_max_kb' => (int) env('VELORA_UPLOAD_MAX_KB', 10240),
        'allowed_upload_mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
    ],
    'cache' => [
        'tags' => [
            'catalog' => 'velora:catalog',
            'search' => 'velora:search',
            'analytics' => 'velora:analytics',
            'recommendations' => 'velora:recommendations',
            'dashboard' => 'velora:dashboard',
            'config' => 'velora:config',
        ],
        'ttl' => [
            'catalog' => 600,
            'search' => 300,
            'analytics' => 900,
            'recommendations' => 1800,
            'dashboard' => 120,
            'config' => 3600,
        ],
    ],
    'backups' => [
        'disk' => env('VELORA_BACKUP_DISK', 'local'),
        'retention_days' => (int) env('VELORA_BACKUP_RETENTION_DAYS', 14),
        'path' => env('VELORA_BACKUP_PATH', 'velora/backups'),
    ],
    'webhooks' => [
        'stripe_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'cloudinary_secret' => env('CLOUDINARY_WEBHOOK_SECRET'),
        'shipping_secret' => env('SHIPPING_WEBHOOK_SECRET'),
        'retry_attempts' => 3,
    ],
    'api' => [
        'default_version' => 'v1',
        'supported_versions' => ['v1', 'v2'],
        'deprecated_versions' => [],
    ],
    'observability' => [
        'slow_query_ms' => (int) env('VELORA_SLOW_QUERY_MS', 500),
        'metrics_retention_days' => (int) env('VELORA_METRICS_RETENTION_DAYS', 30),
    ],
    'rewards' => [
        ['id' => 1, 'name' => 'Complimentary styling session', 'description' => 'A personalized AI stylist consultation.', 'points_cost' => 1200],
        ['id' => 2, 'name' => 'Early access preview', 'description' => 'Shop new arrivals 48 hours before public release.', 'points_cost' => 800],
        ['id' => 3, 'name' => '$25 store credit', 'description' => 'Apply credit toward your next considered purchase.', 'points_cost' => 2000],
        ['id' => 4, 'name' => 'Complimentary shipping', 'description' => 'One complimentary standard shipping redemption.', 'points_cost' => 600],
    ],
    'achievements' => [
        ['id' => 1, 'name' => 'First edit', 'description' => 'Complete your first purchase.', 'progress' => 0],
        ['id' => 2, 'name' => 'Style explorer', 'description' => 'Save three AI outfit recommendations.', 'progress' => 0],
        ['id' => 3, 'name' => 'Circle member', 'description' => 'Earn your first 500 loyalty points.', 'progress' => 0],
    ],
    'vip_tiers' => [
        ['slug' => 'member', 'label' => 'Circle', 'threshold' => 0],
        ['slug' => 'atelier', 'label' => 'Atelier', 'threshold' => 2500],
        ['slug' => 'maison', 'label' => 'Maison', 'threshold' => 10000],
    ],
    'performance' => [
        'slow_request_ms' => (int) env('VELORA_SLOW_REQUEST_MS', 750),
    ],
];
