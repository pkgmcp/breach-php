# Project Structure

# BreachPHP Directory Structure

This document defines the official directory structure of the **BreachPHP** project.

Every directory has a single responsibility and must not contain unrelated code.

The goal is to keep the project predictable, maintainable, and scalable.

---

# Root Structure

```text
breach-php/
│
├── .github/
│   ├── ISSUE_TEMPLATE/
│   │   └── bug_report.md
│   └── PULL_REQUEST_TEMPLATE.md
├── config/
├── database/
├── docs/
├── src/
├── stubs/
├── tests/
│
├── .editorconfig
├── .gitattributes
├── .gitignore
├── composer.json
├── phpstan.neon
├── phpunit.xml
├── pint.json
│
├── README.md
├── CHANGELOG.md
├── CONTRIBUTING.md
├── SECURITY.md
├── SUPPORT.md
└── LICENSE.md
```

---

# Source Directory

```text
src/
│
├── Cache/
├── Commands/
├── Concerns/
├── Config/
├── Console/
├── Contracts/
├── DTO/
├── Enums/
├── Events/
├── Exceptions/
├── Facades/
├── Hash/
├── Helpers/
├── Http/
├── Jobs/
├── Parsers/
├── Providers/
├── Results/
├── Rules/
├── Services/
├── Storage/
├── Support/
├── ValueObjects/
│
├── BreachPHP.php
└── functions.php
```

---

# Folder Responsibilities

## Cache

Responsible for cache implementations.

Examples

```text
Cache/

CacheManager.php

ArrayCache.php

LaravelCache.php

Psr16Cache.php
```

No business logic belongs here.

---

## Commands

Contains Artisan commands.

```text
Commands/

CheckCommand.php

SyncCommand.php

WarmupCommand.php

DoctorCommand.php

HealthCommand.php

StatsCommand.php

VerifyCommand.php

OptimizeCommand.php

CacheClearCommand.php

PruneCommand.php

TestCommand.php
```

Commands should only coordinate services.

---

## Concerns

Reusable traits shared across multiple classes.

Examples

```text
HasConfiguration.php

HasLogger.php

InteractsWithStorage.php
```

Do not place business logic here.

---

## Config

Configuration objects.

Examples

```text
Config.php

Configuration.php
```

---

## Console

Console helpers.

Examples

```text
ProgressBar.php

ConsolePrinter.php
```

---

## Contracts

Interfaces only.

```text
Contracts/

ProviderInterface.php

StorageInterface.php

CacheInterface.php

HashGeneratorInterface.php

ParserInterface.php

PasswordCheckerInterface.php
```

No implementations.

---

## DTO

Immutable data transfer objects.

Examples

```text
PasswordResult.php

ProviderResponse.php

SyncResult.php

HealthReport.php
```

DTOs never contain business logic.

---

## Enums

Native PHP enums.

Examples

```text
ProviderType.php

StorageType.php

CacheDriver.php

SyncStatus.php

HealthStatus.php
```

---

## Events

Package events.

```text
PasswordChecked.php

PasswordSafe.php

PasswordBreached.php

StorageHit.php

ApiHit.php

PrefixSynced.php

SyncFailed.php
```

Events contain data only.

---

## Exceptions

Custom exceptions.

```text
ApiException.php

TimeoutException.php

StorageException.php

ParserException.php

ConfigurationException.php

InvalidPasswordException.php
```

Never use generic exceptions.

---

## Facades

Laravel facades.

```text
BreachPHP.php
```

No business logic.

---

## Hash

Hash generation.

```text
Hash/

Sha1Hasher.php
```

Responsible for

* SHA1
* Prefix
* Suffix

Nothing else.

---

## Helpers

Global helper functions.

```text
functions.php
```

Keep minimal.

---

## Http

HTTP communication.

```text
Http/

HttpClient.php

RequestFactory.php

Response.php
```

Responsibilities

* Requests
* Responses
* Retry
* Timeout

No parsing.

---

## Jobs

Queue jobs.

```text
SyncPrefixJob.php

WarmupJob.php

CleanupJob.php

OptimizeDatabaseJob.php
```

Jobs delegate to services.

---

## Parsers

Convert provider responses.

Examples

```text
HibpParser.php
```

Input

Raw response

Output

DTO

---

## Providers

External breach providers.

```text
Providers/

HibpProvider.php
```

Future

```text
OfflineProvider.php

CustomProvider.php
```

Every provider implements

ProviderInterface.

---

## Results

Result objects returned to developers.

```text
PasswordResult.php

HealthResult.php

SyncResult.php
```

Immutable.

---

## Rules

Laravel validation rules.

```text
NotBreached.php

Breached.php
```

---

## Services

Business logic.

```text
PasswordChecker.php

SyncService.php

HealthService.php

StatisticsService.php

WarmupService.php
```

Services orchestrate the package.

They never perform direct HTTP or database work.

---

## Storage

Persistence layer.

```text
Storage/

DatabaseStorage.php

SQLiteStorage.php

RedisStorage.php
```

Responsible for

* Save
* Lookup
* Update
* Delete

Nothing else.

---

## Support

Shared utilities.

```text
Support/

Collection.php

Str.php

Arr.php
```

Keep lightweight.

---

## ValueObjects

Immutable value objects.

```text
PasswordHash.php

Prefix.php

Suffix.php
```

Self-validating.

---

# Database Structure

```text
database/

migrations/

factories/ (optional)

seeders/ (optional)
```

Only migrations are required for the package.

---

# Configuration

```text
config/

breach.php
```

Published to Laravel applications.

---

# Tests

```text
tests/

Architecture/

Feature/

Fixtures/

Helpers/

Integration/

Unit/

Pest.php

TestCase.php
```

---

## Architecture

Verifies project architecture.

Examples

* Final classes
* Dependency rules
* Namespace rules

---

## Unit

Tests individual classes.

---

## Feature

Tests package features.

---

## Integration

Tests

* Database
* Cache
* HTTP
* Laravel

---

## Fixtures

Static testing data.

---

## Helpers

Testing utilities.

---

# Documentation

```text
docs/

installation.md

quick-start.md

configuration.md

usage.md

validation.md

commands.md

offline-engine.md

database.md

storage.md

providers.md

events.md

testing.md

development.md

release.md

api.md

faq.md

getting-help.md
```

---

# GitHub

```text
.github/

workflows/

ISSUE_TEMPLATE/

PULL_REQUEST_TEMPLATE.md
```

Recommended workflows

* Tests
* Static Analysis
* Coding Standards
* Release

---

# Naming Conventions

## Classes

PascalCase

Example

```text
PasswordChecker
```

---

## Interfaces

Suffix

Interface

Example

```text
StorageInterface
```

---

## Commands

Suffix

Command

Example

```text
SyncCommand
```

---

## Services

Suffix

Service

Example

```text
HealthService
```

---

## DTO

Suffix

Result, Data, or Response

Example

```text
PasswordResult
```

---

## Exceptions

Suffix

Exception

Example

```text
StorageException
```

---

# Dependency Rules

Allowed

```text
Contracts
        ▲
        │
Services
   ▲
   │
Public API
```

Infrastructure implements Contracts.

Services depend only on Contracts.

Public API depends on Services.

---

# Forbidden Dependencies

Never allow

* Services → Facades
* Services → Commands
* Services → HTTP implementation
* Providers → Storage
* DTO → Services
* Events → Database
* Helpers → Business Logic

---

# File Organization Rules

Every file must:

* Have one responsibility.
* Follow PSR-4 autoloading.
* Use strict types.
* Include PHPDoc for public APIs.
* Prefer `final` classes.
* Use constructor dependency injection.
* Avoid static state unless justified.

---

# Scalability Guidelines

When adding new features:

* Add a new Service for business logic.
* Add a new Contract before implementation.
* Add tests first or alongside implementation.
* Update documentation if the public API changes.
* Avoid modifying existing classes when extension through interfaces is possible.

---

# Project Philosophy

The structure of BreachPHP is designed around one principle:

> **Small, focused components with clear responsibilities are easier to understand, test, and maintain than large, multi-purpose classes.**

Every directory, class, and file should contribute to a modular architecture that can evolve without introducing unnecessary complexity.
