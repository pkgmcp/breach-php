# BreachPHP

> **Enterprise-grade password breach detection for PHP 8.4+ and Laravel 13+ using the Have I Been Pwned (HIBP) k-Anonymity API.**

[![PHP Version](https://img.shields.io/badge/PHP-8.4+-777BB4.svg)](#)
[![Laravel](https://img.shields.io/badge/Laravel-13+-FF2D20.svg)](#)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](#)
[![Tests](https://img.shields.io/badge/tests-Passing-brightgreen.svg)](#)
[![Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen.svg)](#)

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

$checker = new BreachPHP();

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

Supported drivers include:

* MySQL
* MariaDB
* PostgreSQL
* SQLite
* Redis
* Laravel Cache
* PSR Cache

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
