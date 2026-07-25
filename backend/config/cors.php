<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_filter(array_map('trim', explode(',', env('FRONTEND_URL', 'http://localhost:5173,http://127.0.0.1:5173')))),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With', 'Accept', 'X-Session-Id', 'X-Api-Version', 'X-Request-Id'],
    'exposed_headers' => ['X-Response-Time', 'X-Api-Version', 'Deprecation', 'Sunset'],
    'max_age' => 0,
    'supports_credentials' => true,
];
