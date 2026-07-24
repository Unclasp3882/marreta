<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Site identity
    |--------------------------------------------------------------------------
    */

    'site_name' => config('app.name'),
    'site_description' => env('APP_DESCRIPTION', 'Chapéu de paywall é marreta!'),
    'site_url' => rtrim((string) config('app.url'), '/'),

    /*
    |--------------------------------------------------------------------------
    | Localization
    |--------------------------------------------------------------------------
    */

    'language' => config('app.locale'),

    /*
    |--------------------------------------------------------------------------
    | Networking
    |--------------------------------------------------------------------------
    */

    'dns_servers' => array_values(array_filter(explode(',', (string) env('DNS_SERVERS', '1.1.1.1,8.8.8.8')))),

    'proxy_url' => env('PROXY_URL'),

    /*
    |--------------------------------------------------------------------------
    | Browser engine (Lightpanda via CDP)
    | BROWSER_WS_ENDPOINT takes precedence; legacy SELENIUM_HOST is honoured
    | as a deprecated fallback (translated into a ws:// endpoint).
    |--------------------------------------------------------------------------
    */

    'browser' => [
        'ws_endpoint' => env('BROWSER_WS_ENDPOINT')
            ?: (env('SELENIUM_HOST') ? 'ws://'.preg_replace('#^https?://#', '', (string) env('SELENIUM_HOST')) : 'ws://localhost:9222'),
        'timeout_ms' => (int) env('BROWSER_TIMEOUT_MS', 30000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache (disk only)
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'disabled' => (bool) env('DISABLE_CACHE', false),
        'directory' => storage_path('app/marreta-cache'),
        'compress_level' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance
    |--------------------------------------------------------------------------
    */

    'cleanup_days' => (int) env('CLEANUP_DAYS', 0),

    /*
    |--------------------------------------------------------------------------
    | Admin (Filament)
    |--------------------------------------------------------------------------
    */

    'admin' => [
        'email' => env('ADMIN_EMAIL', 'admin@marreta.local'),
        'password' => env('ADMIN_PASSWORD', 'password'),
    ],

];
