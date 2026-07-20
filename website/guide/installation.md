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
$result = breach_check('password123');

if ($result->isBreached()) {
    echo $result->count();
}
```

---

# Next Steps

After installation, continue with:

1. **Quick Start** — Perform your first password breach check.
2. **Configuration** — Customize providers, storage, cache, and timeouts.
3. **Usage** — Learn the complete API.
4. **Validation** — Integrate BreachPHP into Laravel validation.
5. **Offline Engine** — Configure local storage and synchronization for improved performance.

Welcome to BreachPHP! You're now ready to integrate secure password breach detection into your application.
