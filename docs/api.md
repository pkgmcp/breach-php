# API Reference

This document describes the public API exposed by **BreachPHP**.

The package intentionally provides a small, stable API while hiding implementation details behind interfaces and immutable objects.

Unless otherwise noted, all examples apply to both Laravel and framework-agnostic PHP applications.

---

# Main Entry Points

BreachPHP can be accessed through several entry points.

## Laravel Facade

```php
use ShamimStack\BreachPHP\Facades\BreachPHP;

$result = BreachPHP::check('password123');
```

---

## Dependency Injection

```php
use ShamimStack\BreachPHP\Contracts\PasswordCheckerInterface;

public function __construct(
    private readonly PasswordCheckerInterface $checker,
) {
}

$result = $this->checker->check('password123');
```

---

## Pure PHP

```php
$result = breach_check('password123');
```

---

## Helper Function

```php
$result = breach_check('password123');
```

---

# Password Checker

## check()

Checks whether a password exists in the configured breach provider.

### Signature

```php
public function check(string $password): PasswordResult;
```

### Parameters

| Parameter   | Type     | Description                 |
| ----------- | -------- | --------------------------- |
| `$password` | `string` | Plaintext password to check |

### Returns

`PasswordResult`

### Example

```php
$result = BreachPHP::check('password123');

if ($result->isBreached()) {
    //
}
```

---

# PasswordResult

`PasswordResult` is an immutable DTO returned by every password check.

## Methods

### isBreached()

Returns whether the password exists in the breach database.

```php
public function isBreached(): bool;
```

Example:

```php
if ($result->isBreached()) {
    //
}
```

---

### count()

Returns the number of times the password appears in known breaches.

```php
public function count(): int;
```

Example:

```php
$count = $result->count();
```

---

### hash()

Returns the SHA-1 hash generated locally.

```php
public function hash(): string;
```

---

### prefix()

Returns the first five SHA-1 characters.

```php
public function prefix(): string;
```

---

### suffix()

Returns the remaining SHA-1 characters.

```php
public function suffix(): string;
```

---

### provider()

Returns the provider that handled the request.

```php
public function provider(): string;
```

Example:

```php
echo $result->provider();
```

---

### source()

Returns the lookup source.

Possible values include:

* `cache`
* `storage`
* `provider`

```php
public function source(): string;
```

This can be useful for diagnostics and performance monitoring.

---

# Validation Rule

## NotBreached

Laravel validation rule.

Example:

```php
use ShamimStack\BreachPHP\Rules\NotBreached;

$request->validate([
    'password' => [
        'required',
        new NotBreached(),
    ],
]);
```

---

# Helper Functions

## breach_check()

```php
breach_check(string $password): PasswordResult
```

Example:

```php
$result = breach_check('password123');
```

---

## breach_is_safe()

```php
breach_is_safe(string $password): bool
```

Returns `true` if the password is not found in any breach.

---

## breach_is_breached()

```php
breach_is_breached(string $password): bool
```

Returns `true` if the password has been compromised.

---

## breach_create_checker()

```php
breach_create_checker(): PasswordChecker
```

Creates a standalone `PasswordChecker` instance for use outside Laravel. Uses HIBP API with SHA-1 hashing and array cache.

---

# Configuration API

## Configuration::fromArray()

Create a configuration instance from an array:

```php
use ShamimStack\BreachPHP\Config\Configuration;

$config = Configuration::fromArray([
    'provider' => 'hibp',
    'storage' => 'database',
    'cache' => 'redis',
    'timeout' => 15,
]);
```

This is useful for standalone PHP usage or when you need to configure the package programmatically.

---

# Exceptions

BreachPHP throws dedicated exceptions.

## ApiException

Raised when the configured provider cannot be contacted or returns an unexpected response.

---

## TimeoutException

Raised when an HTTP request exceeds the configured timeout.

---

## StorageException

Raised when the configured storage driver encounters an error.

---

## ParserException

Raised when a provider response cannot be parsed.

---

## ConfigurationException

Raised when package configuration is invalid or incomplete.

---

## InvalidPasswordException

Raised when the supplied password is invalid for processing.

---

# Events

The package dispatches events for significant operations.

## PasswordChecked

Dispatched after every successful check.

---

## PasswordBreached

Dispatched when a password is found in the breach database.

---

## PasswordSafe

Dispatched when a password is not found.

---

## PrefixSynced

Dispatched after a prefix has been synchronized successfully.

---

## SyncFailed

Dispatched when synchronization fails.

---

## StorageHit

Dispatched when the lookup is resolved from local storage.

---

## ApiHit

Dispatched when the lookup requires the remote provider.

---

# Contracts

The following interfaces define the package's extension points.

| Interface                  | Purpose                       |
| -------------------------- | ----------------------------- |
| `PasswordCheckerInterface` | Password checking service     |
| `ProviderInterface`        | External breach providers     |
| `StorageInterface`         | Local storage implementations |
| `CacheInterface`           | Cache implementations         |
| `HashGeneratorInterface`   | SHA-1 generation              |
| `ParserInterface`          | Provider response parsing     |

Applications should depend on these interfaces rather than concrete implementations.

---

# Storage Interface

Conceptually, storage implementations provide methods similar to:

```php
public function hasPrefix(string $prefix): bool;

public function find(string $prefix, string $suffix): ?int;

public function store(PrefixResponse $response): void;
```

These methods are intended for implementers of custom storage drivers.

---

# Provider Interface

Custom providers should expose behavior similar to:

```php
public function fetch(string $prefix): ProviderResponse;
```

Providers are responsible only for communicating with external breach services.

---

# Result Object Example

```php
$result = BreachPHP::check('password123');

echo $result->isBreached();

echo $result->count();

echo $result->provider();

echo $result->source();
```

---

# Offline Lookup

When local storage is enabled:

```php
$result = BreachPHP::check($password);
```

Lookup order:

1. Cache
2. Local storage
3. Remote provider
4. Store synchronized prefix
5. Return immutable result

No changes to application code are required to benefit from offline lookups.

---

# Thread Safety

All DTOs and value objects are immutable.

This makes them safe to pass between services without unexpected mutation.

---

# Return Values

| Method         | Returns          |
| -------------- | ---------------- |
| `check()`      | `PasswordResult` |
| `isBreached()` | `bool`           |
| `count()`      | `int`            |
| `hash()`       | `string`         |
| `prefix()`     | `string`         |
| `suffix()`     | `string`         |
| `provider()`   | `string`         |
| `source()`     | `string`         |

---

# Data Transfer Objects

BreachPHP uses two namespaces for data objects:

## DTO Namespace (`ShamimStack\BreachPHP\DTO\`)

Internal data transfer objects used for passing data between components:

* `PasswordResult` — Result of a password check
* `ProviderResponse` — Raw response from a provider
* `PrefixResponse` — Parsed prefix data

## Results Namespace (`ShamimStack\BreachPHP\Results\`)

Public-facing result objects returned by commands and services:

* `HealthResult` — Health check result
* `SyncResult` — Synchronization result

The DTO namespace is for internal package communication, while Results is for user-facing return types.

---

# Traits (Concerns)

Reusable traits shared across multiple classes:

## HasConfiguration

Provides access to the package configuration:

```php
use ShamimStack\BreachPHP\Concerns\HasConfiguration;

$this->getConfiguration(); // Returns Configuration instance
```

## HasLogger

Provides access to a PSR-compatible logger:

```php
use ShamimStack\BreachPHP\Concerns\HasLogger;

$this->getLogger(); // Returns LoggerInterface instance
```

## InteractsWithStorage

Provides storage interaction methods:

```php
use ShamimStack\BreachPHP\Concerns\InteractsWithStorage;

$this->getStore(); // Returns StorageInterface instance
```

---

# Public API Stability

The public API follows Semantic Versioning.

Within a major version:

* Existing public methods will remain stable.
* Breaking changes will not be introduced.
* New functionality may be added in a backward-compatible manner.

Major versions may introduce new APIs or replace existing ones when necessary.

---

# Best Practices

* Prefer dependency injection in reusable services.
* Use the Laravel facade only where appropriate.
* Handle package-specific exceptions.
* Enable local storage in production environments.
* Keep the package updated to receive security and performance improvements.
* Program against interfaces instead of concrete implementations.

---

# Next Steps

Continue with:

* **commands.md** — Maintenance and synchronization commands.
* **offline-engine.md** — Local synchronization architecture.
* **development.md** — Contributing and extending the package.

The BreachPHP API is intentionally small, expressive, and stable, allowing applications to integrate password breach detection with minimal code while remaining flexible enough for future extensions.
