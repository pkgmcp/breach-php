# Frequently Asked Questions (FAQ)

This document answers the most common questions about **BreachPHP**.

If your question is not covered here, please check the documentation or open a discussion on the project's GitHub repository.

---

# General

## What is BreachPHP?

BreachPHP is a modern PHP package that checks whether a password has appeared in known public data breaches.

It uses the **Have I Been Pwned (HIBP) Pwned Passwords API** by default and supports an offline engine that can cache synchronized prefix responses for faster, more resilient lookups.

---

## Why should I check passwords against breach data?

Users frequently reuse passwords across multiple services. Even a strong password can be unsafe if it has already appeared in a public breach.

Rejecting known compromised passwords helps reduce the risk of account takeover attacks.

---

## Does BreachPHP store passwords?

No.

BreachPHP never stores plaintext passwords.

Only SHA-1 prefixes, SHA-1 suffixes, and breach counts may be stored when the Offline Engine is enabled.

---

## Are plaintext passwords sent over the internet?

No.

Passwords are hashed locally.

Only the first **five characters** of the SHA-1 hash are sent to the configured provider when required.

This follows the **k-Anonymity** model used by the HIBP Pwned Passwords API.

---

# Installation

## Which PHP versions are supported?

Current releases target:

* PHP 8.4+

Support for additional versions depends on the package roadmap.

---

## Which Laravel versions are supported?

Current releases support:

* Laravel 13+

The package core is framework-independent and can also be used in any PSR-compatible PHP application.

---

## Can I use BreachPHP without Laravel?

Yes.

The core package has no Laravel dependency.

Laravel integration is provided as an optional layer including:

* Service Provider
* Facade
* Validation Rule
* Artisan Commands

---

# Offline Engine

## What is the Offline Engine?

The Offline Engine stores synchronized provider responses locally.

As your application checks more passwords, it gradually builds a local database of SHA-1 prefixes and suffixes, reducing future network requests.

---

## Can BreachPHP work without an internet connection?

Partially.

If the requested SHA-1 prefix has already been synchronized, BreachPHP can perform the lookup entirely from local storage.

If the prefix has never been stored locally, a provider lookup is still required.

---

## Can I download the complete breach database?

BreachPHP is designed around incremental synchronization.

If you choose to build a complete local mirror, ensure that doing so complies with the provider's licensing and usage policies.

---

# Security

## Why does BreachPHP use SHA-1?

SHA-1 is used because it is the format required by the HIBP Pwned Passwords API.

The hash is **not** used for password storage or authentication—it is only used to query breach data.

Applications should continue using modern password hashing algorithms (such as Argon2id or bcrypt) for storing user passwords.

---

## Is SHA-1 secure?

SHA-1 is no longer recommended for cryptographic integrity or password storage.

In BreachPHP, SHA-1 is used solely to implement the provider's lookup protocol and does not replace secure password hashing in your application.

---

## Does BreachPHP replace Laravel's password hashing?

No.

BreachPHP checks whether a password is publicly known.

Password hashing (for example, using Laravel's `Hash` facade) remains responsible for securely storing user passwords.

These serve different purposes and should be used together.

---

## Should I reject every breached password?

In most applications, yes.

However, organizations may choose different policies based on:

* Breach count
* User role
* Internal security requirements
* Regulatory obligations

BreachPHP provides the breach count so applications can implement their own policies.

---

# Performance

## Is every password check an HTTP request?

Not necessarily.

Lookup order is:

1. Cache
2. Local storage
3. Provider

Once a prefix has been synchronized, future lookups for that prefix typically avoid remote requests.

---

## Does the package cache results?

Yes.

BreachPHP can use your configured cache driver to reduce repeated lookups and improve performance.

---

## Which cache driver is recommended?

For production environments:

* Redis

For development:

* File
* Array

---

# Database

## What data is stored?

When the Offline Engine is enabled:

* SHA-1 prefixes
* SHA-1 suffixes
* Breach counts
* Synchronization metadata

Plaintext passwords are never stored.

---

## Does the database grow indefinitely?

The local database grows as additional prefixes are synchronized.

The size depends on:

* Application traffic
* Warmup strategy
* Manual synchronization
* Maintenance policies

Regular monitoring and optimization are recommended.

---

# Development

## Can I add my own provider?

Yes.

Implement `ProviderInterface` and register your provider through the package configuration.

---

## Can I create my own storage driver?

Yes.

Implement `StorageInterface` and register the driver.

The rest of the package remains unchanged.

---

## Can I contribute?

Absolutely.

Contributions are welcome.

Please read:

* `../CONTRIBUTING.md`
* `development.md`
* `ai-rules.md`

before opening a pull request.

---

# Testing

## Does the package require internet access during testing?

No.

Tests should use mocked HTTP clients or fake providers.

The automated test suite should be fully deterministic.

---

## Which testing framework is used?

The project uses:

* Pest
* PHPUnit
* Laravel Testbench
* Mockery
* Infection
* PHPStan

---

# Troubleshooting

## Provider requests are failing

Verify:

* Internet connectivity
* HTTPS access
* Provider availability
* Timeout configuration

Run:

```bash id="4h2h3m"
php artisan breach:doctor
```

---

## Local storage is not being used

Check:

* Storage driver configuration
* Database migrations
* Successful synchronization
* Cache configuration

Review:

```bash id="tjlwm6"
php artisan breach:stats
```

---

## Synchronization is slow

Consider:

* Queue-based synchronization
* Redis caching
* Database indexing
* Running `breach:optimize`

---

## I changed the configuration but nothing happened

Laravel may be using cached configuration.

Run:

```bash id="efjlwm"
php artisan config:clear

php artisan cache:clear
```

Then test the installation again:

```bash id="wjlwm0"
php artisan breach:test
```

---

# Future

## Will more providers be supported?

Yes.

Future versions are expected to support additional breach data providers through the existing `ProviderInterface`.

---

## Will Redis storage be supported?

Redis-backed storage is planned as a future enhancement for workloads that benefit from in-memory persistence.

---

## Will BreachPHP support additional frameworks?

The package core is framework-independent.

Integration layers for other PHP frameworks may be added in the future without changing the core architecture.

---

# Best Practices

* Enable local storage in production.
* Use Redis for caching where available.
* Schedule synchronization jobs.
* Monitor storage growth.
* Keep the package updated.
* Use dependency injection instead of concrete implementations.
* Review release notes before upgrading.

---

# Need More Help?

If you cannot find the answer here:

1. Review the relevant documentation in the `docs/` directory.
2. Check the project's GitHub issues and discussions.
3. Run `php artisan breach:doctor` to diagnose configuration problems.
4. Open a new issue with:

   * PHP version
   * Laravel version (if applicable)
   * Package version
   * Steps to reproduce
   * Relevant logs (excluding sensitive information)

Providing detailed information helps reproduce and resolve issues more quickly.
