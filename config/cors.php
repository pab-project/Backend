<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Atur allowed_origins di .env:
    |   Development : CORS_ALLOWED_ORIGINS=*
    |   Production  : CORS_ALLOWED_ORIGINS=https://yourdomain.com,https://app.yourdomain.com
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Baca dari .env — pisah dengan koma jika lebih dari satu origin
    'allowed_origins' => array_map(
        'trim',
        explode(',', env('CORS_ALLOWED_ORIGINS', '*'))
    ),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 86400, // cache preflight 24 jam

    'supports_credentials' => env('CORS_SUPPORTS_CREDENTIALS', false),

];
