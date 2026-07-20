# BreachPHP

> **Enterprise-grade password breach detection for PHP 8.4+ and Laravel 13+ using the Have I Been Pwned (HIBP) k-Anonymity API.**

[![PHP Version](https://img.shields.io/badge/PHP-8.4+-777BB4.svg)](https://www.php.net/releases/8.4/en.php)
[![Laravel](https://img.shields.io/badge/Laravel-13+-FF2D20.svg)](https://laravel.com)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE.md)
[![Tests](https://github.com/shamimstack/breach-php/actions/workflows/tests.yml/badge.svg)](https://github.com/shamimstack/breach-php/actions)
[![Packagist Downloads](https://img.shields.io/packagist/dt/shamimstack/breach-php)](https://packagist.org/packages/shamimstack/breach-php)

---

## Overview

**BreachPHP** is a modern, framework-friendly PHP package that helps detect whether a password has appeared in known data breaches without ever sending the plaintext password over the network.

The package uses the **Have I Been Pwned (HIBP) Pwned Passwords API** with the **k-Anonymity** model, ensuring only the first five characters of the password's SHA-1 hash are transmitted.

Beyond simple API integration, BreachPHP provides an extensible architecture with optional local storage, offline lookups for previously synchronized data, Laravel integration, health diagnostics, and developer tooling.

---

# Features

* ✅ PHP 8.4+
* ✅ Laravel 13+
* ✅ Framework agnostic core
* ✅ HIBP k-Anonymity support
* ✅ Never transmits plaintext passwords
* ✅ Immutable DTO responses
* ✅ Clean Architecture
* ✅ SOLID & DRY design
* ✅ PSR compliant
* ✅ Laravel Validation Rule
* ✅ Laravel Facade
* ✅ Helper functions
* ✅ Config publishing
* ✅ Database storage
* ✅ Offline lookup engine
* ✅ Prefix synchronization
* ✅ Queue support
* ✅ Cache support
* ✅ Redis support
* ✅ SQLite support
* ✅ Health diagnostics
* ✅ Artisan commands
* ✅ Pest tests
* ✅ PHPStan Max Level
* ✅ Laravel Pint
* ✅ GitHub Actions

---

# Why BreachPHP?

Most password breach libraries simply call the HIBP API and return a response.

BreachPHP goes further by providing:

* A reusable service-oriented architecture
* Optional local breach database
* Incremental synchronization
* Background jobs
* Storage abstraction
* Multiple drivers
* Rich command-line tools
* Laravel-first developer experience
* Enterprise-ready code quality

---

# Installation

```bash
composer require shamimstack/breach-php
```

The package automatically includes `php-http/guzzle7-adapter` for HTTP communication with the HIBP API — no additional HTTP client setup is required.

Publish the configuration file:

```bash
php artisan vendor:publish --tag=breachphp-config
```

Publish migrations:

```bash
php artisan vendor:publish --tag=breachphp-migrations
```

Run migrations:

```bash
php artisan migrate
```

---

# Configuration

All configuration options can be set via `.env` variables or by publishing and editing `config/breach.php`:

| Key | Default | Env Variable | Description |
|---|---|---|---|
| `provider` | `"hibp"` | `BREACH_PROVIDER` | Breach data source |
| `storage` | `"database"` | `BREACH_STORAGE` | Storage driver: `"database"`, `"sqlite"`, `"none"` |
| `cache` | `"array"` | `BREACH_CACHE` | Cache driver: `"array"`, `"redis"`, `"psr16"`, `"laravel"` |
| `timeout` | `10` | `BREACH_TIMEOUT` | HTTP request timeout (seconds) |
| `connect_timeout` | `5` | `BREACH_CONNECT_TIMEOUT` | HTTP connect timeout (seconds) |
| `retries` | `3` | `BREACH_RETRIES` | Number of HTTP retries |
| `retry_delay` | `250` | `BREACH_RETRY_DELAY` | Delay between retries (milliseconds) |
| `store_prefixes` | `true` | `BREACH_STORE_PREFIXES` | Auto-store prefix responses locally |
| `table_prefix` | `"breachphp_"` | `BREACH_TABLE_PREFIX` | Database table name prefix |
| `queue.enabled` | `false` | `BREACH_QUEUE_ENABLED` | Enable Laravel queue support |
| `queue.connection` | env `QUEUE_CONNECTION` | `BREACH_QUEUE_CONNECTION` | Queue connection name |

---

# Quick Start

```php
use ShamimStack\BreachPHP\Facades\BreachPHP;

$result = BreachPHP::check('password123');

if ($result->isBreached()) {
    echo $result->count();
}
```

---

# Pure PHP

```php
use ShamimStack\BreachPHP\BreachPHP;
use ShamimStack\BreachPHP\Services\PasswordChecker;
use ShamimStack\BreachPHP\Providers\HibpProvider;
use ShamimStack\BreachPHP\Http\HttpClient;
use ShamimStack\BreachPHP\Http\RequestFactory;
use ShamimStack\BreachPHP\Hash\Sha1Hasher;
use ShamimStack\BreachPHP\Parsers\HibpParser;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Discovery\Psr17FactoryDiscovery;

$client = Psr18ClientDiscovery::find();
$requestFactory = new RequestFactory(Psr17FactoryDiscovery::findRequestFactory());
$httpClient = new HttpClient($client, $requestFactory);

$checker = new PasswordChecker(
    provider: new HibpProvider($httpClient, new HibpParser()),
    hasher: new Sha1Hasher(),
);

$result = $checker->check('password123');

$result->isSafe();
```

---

# Laravel Validation

```php
use ShamimStack\BreachPHP\Rules\NotBreached;

'password' => [
    'required',
    'string',
    'min:12',
    new NotBreached(),
];
```

Or with custom message:

```php
use ShamimStack\BreachPHP\Rules\NotBreached;

'password' => [
    'required',
    'string',
    new NotBreached(message: 'The :attribute has appeared in a data breach.'),
];
```

---

# Response

```php
$result->isBreached();

$result->isSafe();

$result->count();

$result->hash();

$result->prefix();

$result->suffix();
```

---

# Offline Engine

BreachPHP supports building a local breach database over time.

Lookup order:

```
Password
      │
      ▼
Generate SHA1
      │
      ▼
Local Storage
      │
 ┌────┴────┐
 │         │
Found    Missing
 │         │
 ▼         ▼
Return   HIBP API
            │
            ▼
      Save Locally
            │
            ▼
         Return
```

Previously synchronized prefixes remain available even if the HIBP service is temporarily unavailable.

---

# Artisan Commands

## Check Password

```bash
php artisan breach:check
```

## Synchronize

```bash
php artisan breach:sync
```

## Warmup

```bash
php artisan breach:warmup
```

## Health

```bash
php artisan breach:health
```

## Doctor

```bash
php artisan breach:doctor
```

## Test

```bash
php artisan breach:test
```

## Statistics

```bash
php artisan breach:stats
```

## Verify Storage

```bash
php artisan breach:verify
```

## Optimize Database

```bash
php artisan breach:optimize
```

## Clear Cache

```bash
php artisan breach:cache-clear
```

## Prune

```bash
php artisan breach:prune
```

---

# Storage Drivers

Supported storage drivers include:

* MySQL / MariaDB
* PostgreSQL
* SQLite
* None (in-memory only)

# Cache Drivers

* Array (default, no persistence)
* Redis
* PSR-16
* Laravel Cache

---

# Architecture

The package is organized into dedicated components:

* Contracts
* Services
* Drivers
* Storage
* Providers
* DTO
* Value Objects
* Events
* Jobs
* Rules
* Commands
* Exceptions
* Support

This separation keeps the package maintainable, testable, and easy to extend.

---

# Testing

The package uses:

* Pest
* PHPUnit
* PHPStan
* Laravel Pint
* Rector
* Infection (Mutation Testing)

Target:

* High test coverage
* Maximum static analysis level
* Production-ready quality

---

# Documentation

Complete documentation is available in the `docs/` directory.

Topics include:

* Installation
* Configuration
* Usage
* Validation
* Offline Engine
* Database
* Commands
* Events
* Storage
* Testing
* API Reference
* FAQ
* Development Guide

---

# Roadmap

### Version 1.0

* HIBP Provider
* Password Checking
* Laravel Integration
* Validation Rule
* Artisan Commands

### Version 1.1

* Local Database
* Offline Lookup
* Cache Drivers
* Prefix Synchronization

### Version 1.2

* Queue Support
* Warmup Engine
* Health Monitoring
* Statistics

### Version 2.0

* Multiple Providers
* Plugin System
* Advanced Offline Engine
* Performance Optimizations

---

# Security

* Plaintext passwords are never transmitted.
* SHA-1 hashes are generated locally.
* Only the first five characters of the SHA-1 hash are sent to the HIBP API (k-Anonymity).
* Sensitive information is never logged by default.
* Configurable retry, timeout, and cache settings.

---

# Contributing

Contributions are welcome.

Please read:

* `CONTRIBUTING.md`
* `docs/ai-rules.md`
* `docs/architecture.md`

before opening a pull request.

---

# License

This package is open-sourced software licensed under the **MIT License**.

See `LICENSE.md` for details.

---

# Author

**ShamimStack**

GitHub: https://github.com/shamimstack

---

# Acknowledgements

* Have I Been Pwned
* Laravel
* PHP-FIG
* The PHP Community

---

> **BreachPHP** aims to become the standard password breach detection library for the PHP ecosystem by combining strong security practices, a clean developer experience, and an extensible enterprise-grade architecture.
