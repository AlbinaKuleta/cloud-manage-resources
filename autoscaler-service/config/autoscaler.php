<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Autoscaler Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurimi i threshold-eve dhe parametrave për auto-scaling
    |
    */

    'monitoring' => [
        'service_url' => env('MONITORING_SERVICE_URL', 'http://127.0.0.1:8002'),
        'timeout' => env('MONITORING_SERVICE_TIMEOUT', 5),
        'interval' => env('MONITORING_INTERVAL', 30), // sekonda
    ],

    'resource' => [
        'service_url' => env('RESOURCE_SERVICE_URL', 'http://127.0.0.1:8001'),
        'timeout' => env('RESOURCE_SERVICE_TIMEOUT', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Scaling Thresholds
    |--------------------------------------------------------------------------
    |
    | Threshold-e për scale up dhe scale down
    |
    */

    'thresholds' => [
        'scale_up' => [
            'cpu' => env('SCALE_UP_CPU_THRESHOLD', 80), // %
            'memory' => env('SCALE_UP_MEMORY_THRESHOLD', 85), // %
            'disk' => env('SCALE_UP_DISK_THRESHOLD', 90), // %
            'duration' => env('SCALE_UP_DURATION', 120), // sekonda (2 minuta)
        ],
        'scale_down' => [
            'cpu' => env('SCALE_DOWN_CPU_THRESHOLD', 50), // %
            'memory' => env('SCALE_DOWN_MEMORY_THRESHOLD', 50), // %
            'disk' => env('SCALE_DOWN_DISK_THRESHOLD', 50), // %
            'duration' => env('SCALE_DOWN_DURATION', 300), // sekonda (5 minuta)
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Scaling Actions
    |--------------------------------------------------------------------------
    |
    | Konfigurimi i veprimeve të scaling
    |
    */

    'scaling' => [
        'enabled' => env('AUTOSCALER_ENABLED', true),
        'min_resources' => env('MIN_RESOURCES', 1),
        'max_resources' => env('MAX_RESOURCES', 10),
        'scale_up_resource' => [
            'name_prefix' => 'auto-scaled-',
            'type' => 'vm',
            'status' => 'active',
            'cpu_capacity' => env('SCALE_UP_CPU', 5), // %
            'memory_capacity' => env('SCALE_UP_MEMORY', 500), // MB
            'storage_capacity' => env('SCALE_UP_STORAGE', 10), // GB
            'region' => 'auto',
        ],
    ],
];

