<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurimi i serviseve që API Gateway rutejë kërkesat
    |
    */

    'services' => [
        'auth' => [
            'url' => env('AUTH_SERVICE_URL', 'http://127.0.0.1:8003'),
            'timeout' => env('AUTH_SERVICE_TIMEOUT', 5),
            'prefix' => '/api/auth',
        ],
        'resource' => [
            'url' => env('RESOURCE_SERVICE_URL', 'http://127.0.0.1:8001'),
            'timeout' => env('RESOURCE_SERVICE_TIMEOUT', 5),
            'prefix' => '/api/resources',
        ],
        'monitoring' => [
            'url' => env('MONITORING_SERVICE_URL', 'http://127.0.0.1:8002'),
            'timeout' => env('MONITORING_SERVICE_TIMEOUT', 5),
            'prefix' => '/api/monitoring',
        ],
        'autoscaler' => [
            'url' => env('AUTOSCALER_SERVICE_URL', 'http://127.0.0.1:8004'),
            'timeout' => env('AUTOSCALER_SERVICE_TIMEOUT', 5),
            'prefix' => '/api/autoscaler',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Konfigurimi i rate limiting për API Gateway
    |
    */

    'rate_limit' => [
        'enabled' => env('RATE_LIMIT_ENABLED', true),
        'max_requests' => env('RATE_LIMIT_MAX_REQUESTS', 100),
        'decay_minutes' => env('RATE_LIMIT_DECAY_MINUTES', 1),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    |
    | Konfigurimi i authentication për API Gateway
    |
    */

    'auth' => [
        'service_url' => env('AUTH_SERVICE_URL', 'http://127.0.0.1:8003'),
        'timeout' => env('AUTH_SERVICE_TIMEOUT', 5),
        'cache_ttl' => env('AUTH_CACHE_TTL', 60), // Cache për 60 sekonda
    ],
];

