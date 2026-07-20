# Style Guide

This document defines the coding, naming, and documentation standards for **BreachPHP**.

The goal is to maintain a clean, consistent, and maintainable codebase that is easy to understand and extend.

When this guide conflicts with an automated tool (such as Laravel Pint or PHP-CS-Fixer), the automated tool takes precedence for formatting, while this guide governs design and consistency.

---

# General Principles

Code should be:

* Readable
* Predictable
* Consistent
* Testable
* Explicit
* Maintainable

Prefer simple solutions over clever ones.

Write code for humans first.

---

# PHP Version

Target:

```text
PHP 8.4+
```

Use modern PHP features where they improve readability without reducing clarity.

---

# File Structure

Every PHP file should begin with:

```php
<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP;
```

Imports should be grouped and sorted alphabetically.

Example:

```php
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use ShamimStack\BreachPHP\Contracts\ProviderInterface;
```

Avoid unused imports.

---

# Class Design

Prefer:

* `final` classes
* Constructor dependency injection
* Readonly properties
* Small classes
* Single responsibility

Example:

```php
final readonly class PasswordChecker
{
    public function __construct(
        private ProviderInterface $provider,
        private StorageInterface $storage,
    ) {}
}
```

Avoid inheritance unless it provides a clear architectural benefit.

---

# Methods

Methods should:

* Perform one task
* Be easy to understand
* Return early where appropriate
* Avoid deep nesting
* Have explicit return types

Good:

```php
public function isBreached(string $password): bool
{
    return $this->check($password)->isBreached();
}
```

Avoid methods that perform multiple unrelated responsibilities.

---

# Naming

Use descriptive names.

## Classes

Good:

```text
PasswordChecker

HashGenerator

ProviderResponse

DatabaseStorage

PrefixSynchronizer
```

Avoid:

```text
Manager

Helper

Utility

Processor

ServiceHelper
```

---

## Methods

Methods should describe actions.

Good:

```text
check()

store()

find()

fetch()

parse()

dispatch()

verify()
```

Avoid vague names such as:

```text
do()

run()

process()

executeStuff()
```

---

## Variables

Use meaningful names.

Good:

```php
$password

$prefix

$suffix

$provider

$result
```

Avoid:

```php
$a

$data

$tmp

$x

$obj
```

except for short loop variables.

---

# Constants

Prefer descriptive constant names.

Example:

```php
public const PREFIX_LENGTH = 5;
```

Avoid magic numbers.

---

# Interfaces

Interfaces should describe capabilities.

Examples:

```text
ProviderInterface

StorageInterface

CacheInterface

ParserInterface
```

Do not prefix interfaces with `I`.

---

# DTOs

DTOs should:

* Be immutable
* Contain data only
* Have no business logic

Example:

```php
final readonly class PasswordResult
{
}
```

---

# Value Objects

Value Objects should:

* Be immutable
* Validate themselves
* Implement value equality where appropriate

Examples:

```text
Sha1Hash

Prefix

Suffix
```

---

# Exceptions

Use dedicated exceptions.

Good:

```text
ApiException

StorageException

ParserException

ConfigurationException
```

Avoid throwing generic `Exception`.

---

# Documentation

Every public class should include a concise PHPDoc summary.

Example:

```php
/**
 * Checks whether a password exists in breach data.
 */
final class PasswordChecker
{
}
```

Document public methods when the intent, parameters, or return value is not immediately obvious.

Avoid redundant comments that simply repeat the code.

---

# Formatting

Formatting is handled by Laravel Pint.

General expectations:

* Four spaces for indentation
* One class per file
* One interface per file
* One enum per file
* Trailing commas in multiline arrays and parameter lists where supported
* No trailing whitespace

---

# Dependency Injection

Always inject dependencies.

Good:

```php
public function __construct(
    private ProviderInterface $provider,
) {}
```

Avoid:

```php
$provider = new HIBPProvider();
```

inside services.

---

# Architecture Rules

The Domain layer must never depend on:

* Laravel
* Database implementations
* HTTP clients
* Console components

Infrastructure depends on the Domain—not the other way around.

---

# Testing Style

Test names should describe behavior.

Good:

```php
it('detects a breached password');

it('stores synchronized prefixes');

it('throws an exception when the provider is unavailable');
```

Avoid generic names such as:

```php
test1

providerTest

works
```

Keep tests focused on a single behavior.

---

# Logging

Log useful metadata.

Good:

* Provider name
* Lookup source
* Duration
* Synchronization status

Never log:

* Plaintext passwords
* Full SHA-1 hashes
* API keys
* Secrets

---

# Security

Never:

* Store plaintext passwords
* Send plaintext passwords
* Disable TLS verification in production
* Expose sensitive configuration values

Security should be considered in every design decision.

---

# Configuration

Configuration keys should be:

* Lowercase
* Snake case
* Predictable

Example:

```php
'connect_timeout'

'storage'

'cache'
```

---

# Artisan Commands

Commands should:

* Use the `breach:` namespace
* Produce clear, concise output
* Return standard exit codes
* Support `--help`

Examples:

```text
breach:test

breach:sync

breach:doctor
```

---

# Events

Event names should describe completed or meaningful actions.

Examples:

```text
PasswordChecked

PasswordBreached

SyncCompleted
```

Prefer past-tense names for completed actions.

---

# Folder Organization

Group files by responsibility.

Example:

```text
Providers/

Storage/

Parsers/

Commands/

DTO/

Contracts/
```

Avoid miscellaneous folders such as:

```text
Misc/

Utils/

Helpers2/
```

---

# Git

Use Conventional Commits where practical.

Examples:

```text
feat:

fix:

docs:

test:

refactor:

perf:

chore:
```

Keep commits focused and descriptive.

---

# Documentation Style

Documentation should:

* Use Markdown headings consistently
* Include practical examples
* Explain *why*, not only *how*
* Avoid unnecessary jargon
* Stay synchronized with the codebase

Every public feature should be documented.

---

# Best Practices

* Favor composition over inheritance.
* Program against interfaces.
* Prefer immutable objects.
* Keep public APIs small and stable.
* Minimize dependencies.
* Write tests for all new functionality.
* Refactor for clarity when necessary.
* Leave the codebase better than you found it.

---

# Guiding Principle

Every contribution should improve one or more of the following:

* Readability
* Maintainability
* Testability
* Security
* Performance
* Developer experience

If a change makes the code more difficult to understand without providing a clear benefit, reconsider the approach.
