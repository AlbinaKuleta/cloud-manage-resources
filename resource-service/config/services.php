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

    'monitoring' => [
        'url' => env('MONITORING_SERVICE_URL', 'http://127.0.0.1:8002'),
        'timeout' => env('MONITORING_SERVICE_TIMEOUT', 5),
    ],

    'auth' => [
        'url' => env('AUTH_SERVICE_URL', 'http://127.0.0.1:8000'),
        'timeout' => env('AUTH_SERVICE_TIMEOUT', 5),
    ],
];
