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

    'proxy_url' => env('PROXY_URL'),

    /*
    |--------------------------------------------------------------------------
    | Browser engine (Lightpanda via CDP)
    |--------------------------------------------------------------------------
    */

    'browser' => [
        // A trailing path is required: the underlying WebSocket client rejects a URI with no path.
        // Left null when unset — the browser fetch strategy is skipped in that case.
        'ws_endpoint' => env('BROWSER_WS_ENDPOINT') ? rtrim((string) env('BROWSER_WS_ENDPOINT'), '/').'/' : null,
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
    | Admin (Filament)
    |--------------------------------------------------------------------------
    */

    'admin' => [
        'email' => env('ADMIN_EMAIL', 'admin@marreta.local'),
        'password' => env('ADMIN_PASSWORD', 'password'),
    ],

];
