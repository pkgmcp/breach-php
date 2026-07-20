# Development

This guide explains the development workflow, coding standards, and contribution process for **BreachPHP**.

The project is built around **Clean Architecture**, **SOLID principles**, and modern PHP best practices. Every contribution should improve maintainability, testability, and long-term stability.

---

# Development Philosophy

BreachPHP follows these core principles:

* Framework-independent core
* Laravel integration as a separate layer
* Dependency Injection by default
* Interface-driven design
* Immutable DTOs and Value Objects
* Small, focused classes
* Comprehensive automated testing
* Backward compatibility within major versions

When in doubt, prefer **clarity over cleverness**.

---

# Requirements

## Runtime

* PHP 8.4+
* Composer 2.x

## Development

* Git
* SQLite (recommended)
* Laravel Testbench
* Xdebug or PCOV (optional, for coverage)

---

# Project Structure

```text
breach-php/
├── config/
├── database/
├── docs/
├── src/
├── tests/
├── .github/
├── composer.json
├── phpunit.xml
├── phpstan.neon
├── rector.php
└── pint.json
```

The `src/` directory contains production code. Tests and documentation must evolve alongside it.

---

# Installation

Clone the repository:

```bash
git clone https://github.com/shamimstack/breach-php.git

cd breach-php
```

Install dependencies:

```bash
composer install
```

Run the test suite:

```bash
composer test
```

---

# Composer Scripts

Recommended Composer scripts:

| Command             | Purpose                              |
| ------------------- | ------------------------------------ |
| `composer test`     | Run all tests                        |
| `composer analyse`  | Run PHPStan                          |
| `composer format`   | Run Laravel Pint                     |
| `composer mutation` | Run Infection                        |
| `composer coverage` | Generate coverage report             |
| `composer ci`       | Execute the full CI pipeline locally |

Running `composer ci` before opening a pull request is strongly encouraged.

---

# Coding Standards

The project follows:

* PSR-12
* PSR-4
* PSR-17
* PSR-18
* PSR-16

Additional rules:

* `declare(strict_types=1);`
* Constructor property promotion
* Readonly properties where applicable
* Typed properties
* Return types on all public methods
* Final classes unless extension is intentional

---

# Architecture Rules

## Domain Layer

Contains:

* DTOs
* Value Objects
* Contracts
* Domain services
* Exceptions

The domain layer must not depend on Laravel or infrastructure.

---

## Infrastructure Layer

Contains:

* HTTP providers
* Database storage
* Cache implementations
* Console commands

Infrastructure depends on the domain—not the other way around.

---

## Laravel Integration

Contains:

* Service Provider
* Facade
* Validation Rule
* Configuration
* Commands

Keep framework-specific code isolated from the core package.

---

# Dependency Injection

Always depend on interfaces.

Good:

```php
public function __construct(
    private readonly ProviderInterface $provider,
) {}
```

Avoid instantiating concrete implementations directly within services.

---

# Class Design

Classes should:

* Have a single responsibility
* Be small and cohesive
* Be easy to test
* Avoid hidden side effects

Prefer composition over inheritance.

---

# Naming Conventions

Use descriptive names.

Examples:

```text
PasswordChecker

HashGenerator

DatabaseStorage

ProviderResponse

PasswordResult
```

Avoid vague names such as:

```text
Helper

Manager

Utility

Processor
```

---

# Adding a Feature

Typical workflow:

1. Create or update the relevant contract.
2. Implement the feature.
3. Write unit tests.
4. Write feature/integration tests.
5. Update documentation.
6. Verify backward compatibility.

---

# Adding a Provider

1. Implement `ProviderInterface`.
2. Register the provider.
3. Add configuration support.
4. Write provider tests.
5. Update documentation.

Providers should focus solely on external communication.

---

# Adding a Storage Driver

1. Implement `StorageInterface`.
2. Register the driver.
3. Add configuration options.
4. Write storage tests.
5. Update documentation.

Storage drivers should contain persistence logic only.

---

# Testing

Before submitting changes:

```bash
composer test
```

Static analysis:

```bash
composer analyse
```

Formatting:

```bash
composer format
```

Mutation testing:

```bash
composer mutation
```

All required checks should pass before a pull request is opened.

---

# Git Workflow

Branch from the latest default branch.

Example:

```text
feature/offline-storage

fix/parser-error

docs/api-reference
```

Keep pull requests focused on a single topic.

---

# Commit Messages

Recommended format:

```text
feat: add sqlite storage driver

fix: prevent duplicate suffix inserts

docs: update api reference

test: improve provider coverage

refactor: simplify password checker
```

Follow Conventional Commits where practical.

---

# Pull Requests

Each pull request should:

* Solve one problem
* Include tests
* Include documentation updates when applicable
* Pass CI
* Be reviewed before merging

Large, unrelated changes should be split into smaller pull requests.

---

# Documentation

Every public feature should include documentation.

Update relevant files such as:

* `README.md`
* `api.md`
* `commands.md`
* `configuration.md`
* `offline-engine.md`

Documentation is considered part of the feature.

---

# Performance Guidelines

Optimize only after measuring.

Focus on:

* Efficient prefix lookups
* Batch inserts
* Indexed queries
* Minimal allocations
* Avoiding unnecessary HTTP requests

Readability should not be sacrificed for premature optimization.

---

# Security Guidelines

Never:

* Store plaintext passwords
* Log plaintext passwords
* Send plaintext passwords to providers
* Commit secrets or credentials
* Bypass TLS validation

Security changes should include corresponding tests.

---

# Backward Compatibility

Within a major version:

* Do not remove public APIs.
* Do not change method signatures incompatibly.
* Prefer additive changes.
* Deprecate before removing functionality in a future major release.

---

# Continuous Integration

Every pull request should pass:

* Code style checks
* Static analysis
* Unit tests
* Feature tests
* Integration tests

No code should be merged while CI is failing.

---

# Release Process

Typical release workflow:

1. Merge approved changes.
2. Update documentation.
3. Update `CHANGELOG.md`.
4. Tag a semantic version.
5. Publish the release.
6. Announce changes.

---

# Definition of Done

A feature is considered complete when:

* Code is implemented.
* Tests pass.
* Documentation is updated.
* Static analysis passes.
* Code is formatted.
* CI succeeds.
* The public API remains consistent.

---

# Best Practices

* Keep classes focused.
* Program against interfaces.
* Prefer immutable objects.
* Write tests for every feature and bug fix.
* Review documentation before merging.
* Avoid introducing unnecessary dependencies.
* Leave the codebase cleaner than you found it.

---

# Getting Help

If you're contributing and have architectural questions:

* Review `architecture.md`.
* Check `ai-rules.md`.
* Read `project-structure.md`.
* Search existing issues and discussions before opening a new one.

---

# Next Steps

Continue with:

* **testing.md** — Testing strategy and tooling.
* **architecture.md** — System architecture and design decisions.
* **api.md** — Public API reference.
* **../CONTRIBUTING.md** — Contributor guidelines and pull request workflow.

Following this workflow helps ensure BreachPHP remains consistent, reliable, and maintainable as the project grows.
