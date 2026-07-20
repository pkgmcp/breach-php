Here's a production-ready `ARCHITECTURE.md` for **BreachPHP**.

# Architecture Document

# BreachPHP

> Enterprise-grade Password Breach Detection Package for PHP 8.4+ & Laravel 13+

Version: **1.0**

---

# 1. Overview

BreachPHP follows **Clean Architecture**, **SOLID**, and **PSR standards** to ensure long-term maintainability, extensibility, and testability.

The package is divided into independent layers with clearly defined responsibilities.

Goals:

* Framework agnostic core
* Laravel-first integration
* Dependency Injection
* Highly testable
* Modular
* Extensible
* Production-ready

---

# 2. Design Principles

## SOLID

* Single Responsibility Principle
* Open / Closed Principle
* Liskov Substitution Principle
* Interface Segregation Principle
* Dependency Inversion Principle

---

## DRY

* No duplicated logic
* Shared services
* Shared abstractions

---

## KISS

Simple public API

Complexity hidden internally.

---

## Composition

Prefer composition over inheritance.

---

## Immutability

DTOs and Value Objects should be immutable.

---

# 3. High-Level Architecture

```text
                 Application
                      │
          ┌───────────┴───────────┐
          │                       │
     Laravel               Pure PHP
          │                       │
          └───────────┬───────────┘
                      │
              Password Checker
                      │
        ┌─────────────┼─────────────┐
        │             │             │
     Hashing      Storage      Provider
        │             │             │
        └─────────────┼─────────────┘
                      │
                 Result Builder
                      │
                      ▼
              Immutable DTO
```

---

# 4. Layer Responsibilities

## Public Layer

Responsible for:

* Public API
* Facade
* Helper
* Validation Rule
* Service Provider

This layer should contain **no business logic**.

---

## Application Layer

Responsible for:

* Password checking workflow
* Orchestration
* Service coordination
* Business rules

Main service:

```
PasswordChecker
```

---

## Domain Layer

Contains:

* DTOs
* Value Objects
* Contracts
* Enums
* Exceptions

No infrastructure dependencies.

---

## Infrastructure Layer

Responsible for:

* HTTP
* Database
* Cache
* Storage
* Providers
* Queue

---

# 5. Request Flow

```
Password

↓

SHA1 Generator

↓

Split

Prefix

Suffix

↓

Storage Lookup

↓

Found?

↓

YES

↓

Return Result

↓

NO

↓

Provider Request

↓

Parse Response

↓

Store

↓

Return DTO
```

---

# 6. Package Structure

```
src/

Commands/

Config/

Console/

Contracts/

DTO/

Events/

Exceptions/

Facades/

Hash/

Http/

Jobs/

Models/

Parsers/

Providers/

Rules/

Services/

Storage/

Support/

ValueObjects/

BreachPHP.php
```

---

# 7. Core Components

## Hash Service

Responsibilities

* Generate SHA1
* Extract Prefix
* Extract Suffix

Never communicates with HTTP.

---

## Password Checker

Responsible for

* Workflow
* Validation
* Coordination

Should never contain HTTP code.

---

## Provider

Responsible for

* External communication

Initial implementation

```
HIBP Provider
```

Future

* Local Provider
* Custom Provider

---

## Parser

Responsible for

Parsing HIBP responses.

No HTTP logic.

No Storage logic.

---

## Storage

Responsible for

* Lookup
* Save
* Update
* Delete

Should not know about HTTP.

---

## Cache

Responsible for

* Cache lookup
* Cache save
* TTL

---

## DTO

Immutable objects.

Example

```
PasswordResult

PrefixResponse

ProviderResponse
```

---

## Value Objects

Immutable

Examples

```
PasswordHash

Prefix

Suffix
```

---

# 8. Dependency Flow

Allowed

```
Application

↓

Contracts

↓

Infrastructure
```

Forbidden

```
Infrastructure

↓

Application
```

Infrastructure must never depend on business logic.

---

# 9. Interfaces

Every service depends on interfaces.

Examples

```
PasswordCheckerInterface

ProviderInterface

StorageInterface

CacheInterface

HashGeneratorInterface

ParserInterface
```

---

# 10. Providers

Initial

```
HIBP Provider
```

Future

```
Offline Provider

Database Provider

Custom Provider
```

Every provider implements

```
ProviderInterface
```

---

# 11. Storage Drivers

Supported

* MySQL
* MariaDB
* PostgreSQL
* SQLite

Future

* Redis
* File

Every driver implements

```
StorageInterface
```

---

# 12. Cache Drivers

Supported

* Laravel Cache
* PSR Cache
* Redis

Every cache driver implements

```
CacheInterface
```

---

# 13. Database Design

## prefixes

```
id
prefix
synced_at
created_at
updated_at
```

---

## suffixes

```
id
prefix_id
suffix
count
created_at
updated_at
```

Indexes

```
(prefix)

(prefix_id, suffix)
```

---

## sync_logs

```
id
prefix
status
started_at
finished_at
duration
error
```

---

# 14. Commands

```
breach:check

breach:sync

breach:warmup

breach:test

breach:doctor

breach:health

breach:stats

breach:verify

breach:optimize

breach:cache-clear

breach:prune
```

Each command should be isolated.

Business logic belongs in services.

---

# 15. Events

Dispatch

```
PasswordChecked

PasswordSafe

PasswordBreached

PrefixSynced

SyncFailed

StorageHit

ApiHit
```

Events must not contain business logic.

---

# 16. Queue

Jobs

```
SyncPrefixJob

WarmupJob

CleanupJob

OptimizeDatabaseJob
```

Jobs should delegate to services.

---

# 17. Exception Strategy

Dedicated exceptions only.

Examples

```
ApiException

StorageException

TimeoutException

ParserException

ConfigurationException

InvalidPasswordException
```

Avoid generic `Exception`.

---

# 18. Configuration

Everything configurable.

Never hardcode values.

Examples

* Timeout
* Retry
* Driver
* Cache
* Queue
* Storage

---

# 19. Testing Architecture

```
tests/

Unit/

Feature/

Integration/

Architecture/

Fixtures/

Helpers/
```

Target

* High coverage
* Fast execution
* Mock external dependencies

---

# 20. Code Quality

Required tools

* Pest
* PHPUnit
* PHPStan (maximum configured level)
* Laravel Pint
* Rector
* Infection
* GitHub Actions

---

# 21. Security Principles

* Never send plaintext passwords.
* Generate SHA-1 locally.
* Secure HTTP communication.
* No sensitive logging.
* Immutable objects.
* Safe exception handling.

---

# 22. Extensibility

New features should be added through interfaces rather than modifying existing implementations.

Examples

* New Providers
* New Storage Drivers
* New Cache Drivers
* New Queue Drivers

Existing business logic should remain unchanged.

---

# 23. Performance Goals

* Efficient prefix lookup
* Indexed database
* Lazy service creation
* Configurable caching
* Low memory usage
* Minimal HTTP requests

---

# 24. Architectural Rules

Always:

* Use constructor dependency injection.
* Depend on interfaces.
* Prefer final classes.
* Keep classes focused.
* Keep methods concise.
* Write immutable DTOs.
* Separate business logic from infrastructure.

Never:

* Place HTTP logic in services.
* Mix storage with parsing.
* Duplicate code.
* Hardcode configuration.
* Store plaintext passwords.
* Bypass contracts.

---

# 25. Summary

BreachPHP is designed as a modular, extensible, and production-ready package.

Its architecture separates business logic, infrastructure, storage, and framework integration, making it easy to maintain, test, and extend while providing a consistent developer experience across PHP applications.

**One architectural suggestion:** Instead of naming folders `Drivers` and `Providers` separately, consider this structure:

```text
src/
├── Providers/      # HIBP, Custom, Offline providers
├── Storage/        # Database, SQLite, Redis storage implementations
├── Cache/          # Cache implementations
├── Http/           # HTTP client & transport
├── Services/       # Business logic
├── Contracts/      # Interfaces
```

This is more descriptive than a generic `Drivers/` directory and scales better as the package grows.
