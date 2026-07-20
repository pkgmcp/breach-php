# Changelog

All notable changes to **BreachPHP** will be documented in this file.

The format is based on **Keep a Changelog**, and this project follows **Semantic Versioning (SemVer)**.

---

## [0.1.0] - 2026-07-20

### Added

* Initial project architecture.
* Framework-independent core.
* Laravel 13 integration.
* HIBP Pwned Passwords API provider.
* Offline Engine with incremental synchronization.
* Database storage driver.
* SQLite storage driver.
* Immutable DTOs and Value Objects.
* PSR-18 HTTP client support.
* PSR-16 cache support.
* Laravel validation rule.
* Laravel facade.
* Helper functions.
* Service container integration.
* Comprehensive Artisan commands.
* Event system.
* Queue support for synchronization.
* Configuration publishing.
* Database migrations.
* Extensive documentation.
* CI/CD workflow.
* PHPStan integration.
* Laravel Pint integration.
* Pest testing framework.
* PHPUnit support.
* Infection mutation testing.

### Changed

* Nothing yet.

### Deprecated

* Nothing.

### Removed

* Nothing.

### Fixed

* Nothing.

### Security

* Plaintext passwords are never stored.
* Plaintext passwords are never transmitted.
* All remote lookups use the k-Anonymity protocol.

---

## [1.0.0] - Initial Release

### Added

#### Core

* Password breach detection service.
* Password checker interface.
* Hash generator.
* Provider abstraction.
* Storage abstraction.
* Parser abstraction.
* Cache abstraction.
* Immutable result objects.

#### Laravel

* Service provider.
* Auto-discovery.
* Facade.
* Validation rule.
* Helper functions.
* Configuration publishing.
* Migration publishing.

#### Offline Engine

* Incremental prefix synchronization.
* Local database storage.
* Offline password lookups.
* Automatic prefix caching.
* Synchronization metadata.

#### Storage

* Database storage.
* SQLite storage.
* Storage statistics.
* Integrity verification.
* Database optimization support.

#### Providers

* Have I Been Pwned provider.
* PSR-18 HTTP client support.
* Configurable timeouts.
* Configurable retry strategy.

#### Commands

* `breach:check`
* `breach:sync`
* `breach:warmup`
* `breach:test`
* `breach:doctor`
* `breach:health`
* `breach:stats`
* `breach:verify`
* `breach:cache-clear`
* `breach:prune`
* `breach:optimize`

#### Events

* PasswordChecked
* PasswordBreached
* PasswordSafe
* SyncStarted
* PrefixSynced
* SyncCompleted
* SyncFailed
* CacheHit
* StorageHit
* ApiHit

#### Documentation

* README
* Product Requirements Document
* Architecture Guide
* AI Development Rules
* Project Structure
* Installation Guide
* Quick Start
* Configuration Guide
* Upgrade Guide
* Usage Guide
* Validation Guide
* Offline Engine Guide
* Commands Guide
* Database Guide
* Storage Guide
* Providers Guide
* Events Guide
* Testing Guide
* Development Guide
* Release Guide
* API Reference
* FAQ
* Getting Help Guide

#### Testing

* Unit tests.
* Feature tests.
* Integration tests.
* Mock providers.
* Fake storage.
* Laravel Testbench integration.

### Security

* Local SHA-1 generation.
* k-Anonymity implementation.
* HTTPS-only provider communication.
* No plaintext password persistence.
* No plaintext password logging.

---

## Versioning Policy

### Patch Releases (`1.0.x`)

Patch releases include:

* Bug fixes
* Documentation improvements
* Performance optimizations
* Security fixes
* Internal refactoring without API changes

---

### Minor Releases (`1.x.0`)

Minor releases may include:

* New commands
* New storage drivers
* New providers
* New events
* New configuration options
* Backward-compatible API additions

---

### Major Releases (`x.0.0`)

Major releases may introduce:

* Breaking API changes
* Architectural improvements
* Removed deprecated features
* Database schema changes
* New extension mechanisms

Migration guidance will always be provided in the corresponding release notes.

---

## Release Notes Guidelines

Every release should document changes using the following sections where applicable:

* **Added** – New features and capabilities.
* **Changed** – Updates to existing functionality.
* **Deprecated** – Features scheduled for removal.
* **Removed** – Deleted functionality.
* **Fixed** – Bug fixes.
* **Security** – Security-related improvements or advisories.

---

## Support Policy

Only the latest major version receives new features.

Security and critical bug fixes may be backported to supported release branches when appropriate.

---

## Acknowledgements

BreachPHP is inspired by the privacy-preserving design of the **Have I Been Pwned Pwned Passwords API** and the broader PHP open-source ecosystem. The project aims to provide a modern, extensible, and production-ready password breach detection package for PHP applications.
