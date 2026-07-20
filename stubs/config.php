<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Provider
    |--------------------------------------------------------------------------
    |
    | The provider to use for retrieving breach data.
    | Supported: "hibp"
    |
    */

    'provider' => env('BREACH_PROVIDER', 'hibp'),

    /*
    |--------------------------------------------------------------------------
    | Storage Driver
    |--------------------------------------------------------------------------
    |
    | The storage driver to use for persisting synchronized prefix data.
    | Supported: "database", "sqlite", "none"
    |
    */

    'storage' => env('BREACH_STORAGE', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Cache Driver
    |--------------------------------------------------------------------------
    |
    | The cache driver to use for caching breach lookups.
    | Supported: "array", "redis", "psr16", "laravel", "none"
    |
    */

    'cache' => env('BREACH_CACHE', 'array'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Configuration
    |--------------------------------------------------------------------------
    |
    | Configure outbound HTTP requests to the breach provider.
    |
    */

    'timeout' => (int) env('BREACH_TIMEOUT', 10),

    'connect_timeout' => (int) env('BREACH_CONNECT_TIMEOUT', 5),

    'retries' => (int) env('BREACH_RETRIES', 3),

    'retry_delay' => (int) env('BREACH_RETRY_DELAY', 250),

    /*
    |--------------------------------------------------------------------------
    | Offline Storage
    |--------------------------------------------------------------------------
    |
    | When enabled, prefix responses are automatically stored locally
    | after being retrieved from the provider. This gradually builds
    | a local breach database over time.
    |
    */

    'store_prefixes' => (bool) env('BREACH_STORE_PREFIXES', true),

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Large synchronization tasks can be executed using Laravel queues.
    |
    */

    'queue' => [
        'enabled' => (bool) env('BREACH_QUEUE_ENABLED', false),
        'connection' => env('BREACH_QUEUE_CONNECTION', env('QUEUE_CONNECTION')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Table Prefix
    |--------------------------------------------------------------------------
    |
    | The prefix to use for database tables.
    |
    */

    'table_prefix' => env('BREACH_TABLE_PREFIX', 'breachphp_'),

];
