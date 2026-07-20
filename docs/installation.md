# Installation

This guide explains how to install and configure **BreachPHP** in both Laravel and framework-agnostic PHP applications.

---

# Requirements

Before installing BreachPHP, ensure your environment meets the following requirements.

## PHP

* PHP **8.4** or later

## Composer

* Latest stable version

## Supported Frameworks

* Laravel 13+
* Pure PHP
* Any PSR-compatible framework

---

# Install via Composer

Install the package using Composer.

```bash
composer require shamimstack/breach-php
```

---

# Laravel Installation

Laravel supports automatic package discovery.

No additional registration is required.

Verify the package is installed:

```bash
php artisan about
```

or

```bash
composer show shamimstack/breach-php
```

---

# Publish Configuration

Publish the configuration file.

```bash
php artisan vendor:publish --tag=breachphp-config
```

This creates:

```text
config/breach.php
```

---

# Publish Database Migrations

If you plan to use the local storage engine, publish the migrations.

```bash
php artisan vendor:publish --tag=breachphp-migrations
```

Run the migrations.

```bash
php artisan migrate
```

---

# Optional: Publish Everything

Publish all package resources.

```bash
php artisan vendor:publish --provider="ShamimStack\BreachPHP\Providers\BreachPHPServiceProvider"
```

---

# Basic Configuration

Open:

```text
config/breach.php
```

Example:

```php
return [

    'provider' => 'hibp',

    'storage' => 'database',

    'cache' => 'array',

    'timeout' => 10,

    'retries' => 3,

    'retry_delay' => 250,

];
```

---

# Pure PHP Installation

BreachPHP can be used without Laravel.

```php
require __DIR__.'/vendor/autoload.php';

$result = breach_check('password123');

if ($result->isBreached()) {
    echo $result->count();
}
```

Dependency injection through a PSR-11 container is recommended for larger applications.

---

# Verify Installation

Laravel users can verify the installation using the built-in command.

```bash
php artisan breach:test
```

Expected output:

```text
✔ Package installed

✔ Configuration loaded

✔ HTTP client available

✔ Storage configured

✔ Cache configured

✔ Provider reachable

Installation successful.
```

---

# First Password Check

Laravel

```php
use ShamimStack\BreachPHP\Facades\BreachPHP;

$result = BreachPHP::check('password123');

if ($result->isBreached()) {
    echo "Password found {$result->count()} times.";
}
```

Pure PHP

```php
$breach = new BreachPHP();

$result = $breach->check('password123');

if ($result->isBreached()) {
    echo $result->count();
}
```

---

# Optional Offline Storage

To reduce API requests and support offline lookups, enable local storage.

Example configuration:

```php
'storage' => 'database',
```

Then synchronize prefixes as needed.

```bash
php artisan breach:sync
```

As passwords are checked, additional prefixes can also be stored automatically, depending on your synchronization strategy.

---

# Cache Configuration

Choose the cache driver that best fits your application.

Example:

```php
'cache' => 'redis',
```

Supported drivers include:

* Laravel Cache
* PSR-16 Cache
* Redis
* Array (development)

---

# Queue Support

For large synchronization jobs, configure a Laravel queue.

Example:

```env
QUEUE_CONNECTION=database
```

Then run a worker.

```bash
php artisan queue:work
```

---

# Troubleshooting

## Package Not Found

Run:

```bash
composer install
composer dump-autoload
```

---

## Configuration Changes Not Applied

Clear Laravel caches.

```bash
php artisan optimize:clear
```

---

## Migration Errors

Ensure your database connection is configured correctly.

```env
DB_CONNECTION=mysql
```

Then rerun:

```bash
php artisan migrate
```

---

## Provider Connection Issues

Run the diagnostics command.

```bash
php artisan breach:doctor
```

This checks:

* Configuration
* Network connectivity
* HTTP client
* Storage
* Cache
* Provider availability

---

# Updating

Update to the latest compatible version.

```bash
composer update shamimstack/breach-php
```

Review the upgrade guide before updating between major versions.

---

# Next Steps

After installation, continue with:

1. **Quick Start** — Perform your first password breach check.
2. **Configuration** — Customize providers, storage, cache, and timeouts.
3. **Usage** — Learn the complete API.
4. **Validation** — Integrate BreachPHP into Laravel validation.
5. **Offline Engine** — Configure local storage and synchronization for improved performance.

Welcome to BreachPHP! You're now ready to integrate secure password breach detection into your application.
