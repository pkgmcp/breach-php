# Configuration

BreachPHP is designed to be configurable without requiring code changes.

All runtime behavior—including providers, storage, caching, HTTP settings, and synchronization—is controlled through the package configuration file.

Laravel users can publish the configuration with:

```bash
php artisan vendor:publish --tag=breachphp-config
```

This creates:

```text
config/breach.php
```

---

# Default Configuration

A typical configuration file may look like this:

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Default Provider
    |--------------------------------------------------------------------------
    */

    'provider' => 'hibp',

    /*
    |--------------------------------------------------------------------------
    | Storage Driver
    |--------------------------------------------------------------------------
    */

    'storage' => 'database',

    /*
    |--------------------------------------------------------------------------
    | Cache Driver
    |--------------------------------------------------------------------------
    */

    'cache' => 'array',

    /*
    |--------------------------------------------------------------------------
    | HTTP
    |--------------------------------------------------------------------------
    */

    'timeout' => 10,

    'connect_timeout' => 5,

    'retries' => 3,

    'retry_delay' => 250,

    /*
    |--------------------------------------------------------------------------
    | Offline Storage
    |--------------------------------------------------------------------------
    */

    'store_prefixes' => true,

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    */

    'queue' => [
        'enabled' => false,
        'connection' => env('QUEUE_CONNECTION'),
    ],

];
```

---

# Provider

The provider determines where breach information is retrieved.

```php
'provider' => 'hibp',
```

Currently supported:

| Provider | Description                           |
| -------- | ------------------------------------- |
| `hibp`   | Have I Been Pwned Pwned Passwords API |

Future releases may introduce additional providers while maintaining the same public API.

---

# Storage Driver

The storage driver controls where synchronized prefix data is stored.

```php
'storage' => 'database',
```

Supported drivers:

| Driver             | Description                       |
| ------------------ | --------------------------------- |
| `database`         | Relational database (recommended) |
| `sqlite`           | SQLite database                   |
| `redis` *(future)* | Redis-backed storage              |
| `file` *(future)*  | File-based storage                |

When storage is disabled, every lookup is performed against the configured provider.

---

# Cache Driver

Cache is used to reduce repeated lookups.

```php
'cache' => 'redis',
```

Supported drivers:

| Driver    | Description                   |
| --------- | ----------------------------- |
| `array`   | In-memory cache (development) |
| `redis`   | Redis cache                   |
| `psr16`   | Any PSR-16 implementation     |
| `laravel` | Laravel Cache facade          |

---

# HTTP Configuration

Configure outbound HTTP requests.

```php
'timeout' => 10,

'connect_timeout' => 5,

'retries' => 3,

'retry_delay' => 250,
```

| Option            | Description                          |
| ----------------- | ------------------------------------ |
| `timeout`         | Maximum request duration (seconds)   |
| `connect_timeout` | Maximum connection time (seconds)    |
| `retries`         | Number of retry attempts             |
| `retry_delay`     | Delay between retries (milliseconds) |

Reasonable defaults are provided for most applications.

---

# Offline Storage

Enable automatic storage of synchronized prefixes.

```php
'store_prefixes' => true,
```

When enabled:

1. A password hash is generated locally.
2. The prefix is searched in local storage.
3. If not found, the provider is queried.
4. The entire prefix response is stored locally.
5. Future lookups for the same prefix no longer require an API request.

This gradually builds a local breach database over time.

---

# Queue

Large synchronization tasks can be executed using Laravel queues.

```php
'queue' => [

    'enabled' => true,

    'connection' => env('QUEUE_CONNECTION'),

],
```

Example environment:

```env
QUEUE_CONNECTION=database
```

Queued synchronization is recommended for production environments.

---

# Environment Variables

Configuration values can be sourced from environment variables.

Example:

```env
BREACH_PROVIDER=hibp

BREACH_STORAGE=database

BREACH_CACHE=redis

BREACH_TIMEOUT=10

BREACH_RETRIES=3
```

Configuration file:

```php
'provider' => env('BREACH_PROVIDER', 'hibp'),

'storage' => env('BREACH_STORAGE', 'database'),

'cache' => env('BREACH_CACHE', 'array'),

'timeout' => (int) env('BREACH_TIMEOUT', 10),
```

This allows different settings across local, staging, and production environments.

---

# Table Prefix

Configure the database table prefix for storage tables.

```php
'table_prefix' => env('BREACH_TABLE_PREFIX', 'breachphp_'),
```

This is useful when:

* Running multiple BreachPHP instances in the same database
* Avoiding table name collisions with other packages
* Organizing tables in multi-tenant applications

---

# Disabling Storage or Cache

Both storage and cache can be disabled by setting the driver to `none`:

```php
'storage' => 'none',

'cache' => 'none',
```

When storage is `none`, every lookup queries the remote provider directly.

When cache is `none`, no caching is performed.

---

# Laravel Configuration Cache

After modifying configuration in production, rebuild Laravel's configuration cache.

```bash
php artisan config:cache
```

During development:

```bash
php artisan optimize:clear
```

---

# Example Configurations

## Development

```php
'storage' => 'sqlite',

'cache' => 'array',

'queue' => [
    'enabled' => false,
],
```

Simple and easy to set up.

---

## Small Production Application

```php
'storage' => 'database',

'cache' => 'redis',

'queue' => [
    'enabled' => true,
],
```

Suitable for most Laravel applications.

---

## High-Traffic Production

```php
'storage' => 'database',

'cache' => 'redis',

'retries' => 5,

'timeout' => 15,

'queue' => [
    'enabled' => true,
],
```

Designed to minimize provider requests while supporting background synchronization.

---

# Configuration Validation

Run the built-in diagnostics command to verify your configuration.

```bash
php artisan breach:doctor
```

The command checks:

* Configuration values
* Provider selection
* Storage driver
* Cache driver
* Queue configuration
* HTTP settings
* Required dependencies

Any detected issues are reported with suggested fixes.

---

# Best Practices

* Use environment variables for deployment-specific settings.
* Enable local storage in production to reduce external API requests.
* Use Redis for caching when available.
* Run synchronization tasks through queues.
* Keep HTTP timeouts reasonable to avoid blocking application requests.
* Review configuration after upgrading to a new major version.

---

# Next Steps

Once configuration is complete, continue with:

* **usage.md** — Learn the complete BreachPHP API.
* **validation.md** — Integrate with Laravel validation rules.
* **offline-engine.md** — Configure and understand the local synchronization engine.
* **commands.md** — Explore the available Artisan commands and maintenance tools.

A well-configured BreachPHP installation provides fast, secure, and reliable password breach detection while minimizing external API usage.
