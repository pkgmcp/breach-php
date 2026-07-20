# AI Rules

# BreachPHP Development Standards

This document defines the mandatory rules that any AI assistant (ChatGPT, Claude, Gemini, GitHub Copilot, Cursor, Windsurf, etc.) must follow when contributing to the BreachPHP codebase.

These rules ensure that every generated file follows the same architecture, coding standards, and design philosophy.

---

# 1. Mission

Your responsibility is to build and maintain **BreachPHP**, an enterprise-grade password breach detection package for PHP.

The package must be:

* Production-ready
* Framework agnostic
* Laravel-friendly
* Highly testable
* Extensible
* Secure
* Easy to maintain

Never generate prototype-quality code.

Always generate production-quality code.

---

# 2. Core Principles

Always follow:

* SOLID
* DRY
* KISS
* Clean Architecture
* PSR Standards
* Dependency Injection
* Composition over Inheritance

Never sacrifice architecture for convenience.

---

# 3. Target Environment

PHP

* 8.4+

Laravel

* 13+
* Future compatible

Composer

* Latest stable

---

# 4. Architecture Rules

Business logic belongs only inside Services.

Never place business logic inside:

* Controllers
* Commands
* Facades
* Service Providers
* Helpers
* Models

Commands should only orchestrate services.

Facades should only expose services.

Providers should only register dependencies.

---

# 5. Folder Responsibilities

## Contracts

Interfaces only.

No implementation.

---

## Services

Business logic only.

No HTTP.

No database queries.

No cache implementation.

---

## Providers

External breach providers.

Example

* HIBP

Must implement ProviderInterface.

---

## Storage

Responsible for

* Save
* Lookup
* Update
* Delete

No HTTP logic.

---

## Cache

Responsible for caching only.

---

## Http

Responsible for HTTP communication only.

---

## DTO

Immutable response objects.

No business logic.

---

## ValueObjects

Immutable objects.

Self-validating.

---

## Commands

No business logic.

Delegate to services.

---

## Rules

Laravel validation only.

---

## Events

Contain event data only.

Never business logic.

---

## Jobs

Delegate to services.

---

## Exceptions

One exception per responsibility.

---

# 6. Dependency Rules

Allowed

Application

↓

Contracts

↓

Infrastructure

Forbidden

Infrastructure

↓

Application

Infrastructure must never depend on business logic.

---

# 7. Dependency Injection

Always use constructor injection.

Never instantiate dependencies directly.

Example

Good

```php
public function __construct(
    ProviderInterface $provider
) {}
```

Bad

```php
$provider = new HibpProvider();
```

---

# 8. Interfaces

Every major component must have an interface.

Examples

* ProviderInterface
* StorageInterface
* CacheInterface
* HashGeneratorInterface
* PasswordCheckerInterface
* ParserInterface

Depend on interfaces.

Never concrete implementations.

---

# 9. Class Design

Prefer

* final classes
* readonly properties
* constructor promotion

Avoid

* mutable state
* public properties

---

# 10. Methods

Methods should:

* have one responsibility
* be concise
* be descriptive
* avoid nested conditions where possible

Prefer early returns.

---

# 11. Naming

Classes

PascalCase

Methods

camelCase

Variables

camelCase

Constants

UPPER_SNAKE_CASE

Interfaces

Suffix with Interface

DTO

Suffix with Data or Result

Exceptions

Suffix with Exception

Commands

Use action names.

Example

SyncCommand

HealthCommand

DoctorCommand

---

# 12. DTO Rules

DTOs must be

* immutable
* readonly
* typed

Never contain business logic.

---

# 13. Value Objects

Must validate themselves.

Examples

PasswordHash

Prefix

Suffix

Never expose invalid state.

---

# 14. Error Handling

Never throw generic Exception.

Always use dedicated exceptions.

Example

ApiException

TimeoutException

StorageException

ParserException

ConfigurationException

InvalidPasswordException

---

# 15. HTTP Rules

HTTP layer must

* handle requests
* handle responses
* handle retries
* handle timeout

It must never contain business rules.

---

# 16. Storage Rules

Storage should know nothing about

* HTTP
* Parsing
* Validation

Only persistence.

---

# 17. Parser Rules

Parser converts raw provider responses into structured objects.

Parser must not

* perform HTTP requests
* access storage
* modify cache

---

# 18. Security Rules

Never

* transmit plaintext passwords
* log passwords
* store plaintext passwords
* expose secrets

Always

* hash locally
* use HTTPS
* validate inputs

---

# 19. Configuration

Never hardcode

* timeout
* retries
* drivers
* cache
* storage

Everything must be configurable.

---

# 20. Public API

Public API should remain simple.

Example

```php
$result = BreachPHP::check($password);

$result->isBreached();

$result->count();
```

Internal complexity must never leak into the public API.

---

# 21. Documentation

Every public class must include PHPDoc.

Every public method must document:

* purpose
* parameters
* return type
* thrown exceptions

Include usage examples where appropriate.

---

# 22. Testing Rules

Every new feature requires tests.

Required test types:

* Unit
* Feature
* Integration

Mock:

* HTTP
* Cache
* Storage

Never rely on external services during automated tests.

---

# 23. Code Quality

Always satisfy:

* Laravel Pint
* PHPStan (maximum configured level)
* Rector
* Pest
* PHPUnit

Code must pass CI before merge.

---

# 24. Performance

Prefer

* lazy loading
* indexed queries
* immutable objects
* dependency injection

Avoid

* repeated HTTP requests
* unnecessary allocations
* duplicate parsing

---

# 25. Backward Compatibility

Public APIs should remain stable within a major version.

Avoid breaking changes unless absolutely necessary.

Document all breaking changes in CHANGELOG.md.

---

# 26. Git Standards

Commit messages should follow Conventional Commits.

Examples

```
feat: add sqlite storage

fix: resolve parser bug

docs: improve installation guide

test: add provider tests

refactor: simplify password checker
```

---

# 27. Pull Request Rules

Every pull request should:

* pass all tests
* pass static analysis
* include documentation updates when required
* avoid unrelated changes
* keep commits focused

---

# 28. Forbidden Practices

Never:

* Duplicate code
* Use global state
* Use service locators in business logic
* Hardcode configuration
* Mix infrastructure with business logic
* Store plaintext passwords
* Ignore interfaces
* Skip tests for new features

---

# 29. Decision Priority

When multiple solutions exist, choose in this order:

1. Security
2. Correctness
3. Maintainability
4. Readability
5. Extensibility
6. Performance
7. Convenience

Never trade security for convenience.

---

# 30. AI Development Workflow

For every requested feature:

1. Understand the requirement.
2. Review the existing architecture.
3. Reuse existing components where possible.
4. Design the change before writing code.
5. Implement one responsibility at a time.
6. Add or update tests.
7. Update documentation if the public API changes.
8. Ensure the code complies with all project standards.

---

# Final Instruction

Every contribution must leave the project in a better state than it was found.

Write code as if it will be maintained for the next 10 years.

Optimize for clarity, correctness, and long-term maintainability rather than short-term speed of implementation.
