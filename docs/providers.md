# Providers

Providers are responsible for retrieving password breach data from external services.

BreachPHP separates provider communication from business logic through the `ProviderInterface`, making it easy to add new breach data sources without modifying the core package.

The default provider is the **Have I Been Pwned (HIBP) Pwned Passwords API**, which uses the **k-Anonymity** model to preserve password privacy.

---

# Overview

Every provider must implement the `ProviderInterface`.

```php id="k8g3pz"
interface ProviderInterface
{
    public function fetch(string $prefix): ProviderResponse;
}
```

Providers have a single responsibility:

* Retrieve breach data for a SHA-1 prefix.

Providers are **not** responsible for:

* Password hashing
* Parsing business rules
* Storage
* Caching
* Validation
* Offline synchronization

---

# Provider Architecture

```text id="pj0e2d"
Password Checker
        │
        ▼
ProviderInterface
        │
 ┌──────┼──────────────┐
 │      │              │
 ▼      ▼              ▼
HIBP   Custom      Future
```

The Password Checker communicates only with the interface.

---

# Default Provider

The default implementation uses the **Have I Been Pwned Pwned Passwords API**.

Workflow:

```text id="v8k8u6"
Password

↓

Generate SHA-1

↓

Split Hash

↓

Send Prefix

↓

Receive Suffix List

↓

Parse Response

↓

Return Result
```

Only the first five SHA-1 characters are transmitted.

The plaintext password is never sent to the provider.

---

# k-Anonymity

The HIBP API implements the k-Anonymity protocol.

Example:

Password

```text id="jlwm35"
password123
```

SHA-1

```text id="z7qf8m"
CBFDAC6008F9CAB4083784CBD1874F76618D2A97
```

Split into:

```text id="6dcnni"
Prefix

CBFDA

Suffix

C6008F9CAB4083784CBD1874F76618D2A97
```

Request:

```text id="v3ytnn"
GET /range/CBFDA
```

Response:

```text id="lzn8b6"
C6008F9CAB4083784CBD1874F76618D2A97:124532
72A4DAB...
A8F3E21...
...
```

The Password Checker compares suffixes locally.

---

# Provider Response

Providers return a `ProviderResponse`.

Conceptually:

```php id="8t5j8y"
ProviderResponse
{
    prefix: string,
    suffixes: array,
}
```

The response is immutable and independent of the storage implementation.

---

# Parsing

Providers return raw data.

Parsing is delegated to a dedicated parser.

```text id="h0k0u8"
HTTP Response

↓

Parser

↓

ProviderResponse

↓

Storage
```

This separation keeps providers focused on communication only.

---

# Configuration

Example configuration:

```php id="sv52db"
'provider' => 'hibp',
```

Future versions may support additional providers.

---

# HTTP Client

Providers use a PSR-18 compatible HTTP client.

Benefits:

* Framework independent
* Easy to mock
* Replaceable
* Testable

The package does not require a specific HTTP client implementation.

---

# Timeouts

Example configuration:

```php id="8rgvtl"
'http' => [

    'timeout' => 10,

    'connect_timeout' => 5,

],
```

Reasonable timeouts prevent applications from blocking indefinitely during provider outages.

---

# Retry Strategy

Optional retry configuration:

```php id="eggpz4"
'http' => [

    'retries' => 2,

],
```

Retries should be conservative to avoid excessive load on external services.

---

# Error Handling

Providers should throw dedicated exceptions.

Examples:

* ApiException
* TimeoutException
* AuthenticationException
* RateLimitException

Avoid exposing transport-specific exceptions directly to application code.

---

# Offline Integration

Lookup flow:

```text id="vfkf5t"
Cache

↓

Storage

↓

Provider

↓

Store Prefix

↓

Return
```

Providers are contacted only when local storage cannot satisfy the request.

---

# Custom Providers

Creating a new provider involves:

1. Implement `ProviderInterface`.
2. Register the provider.
3. Configure it.
4. Add integration tests.

Example:

```php id="b0c2qq"
final class CustomProvider implements ProviderInterface
{
    public function fetch(string $prefix): ProviderResponse
    {
        // ...
    }
}
```

Configuration:

```php id="jlm9rv"
'provider' => 'custom',
```

---

# Multiple Providers

Future releases may support provider failover.

Example:

```text id="3rkkmy"
Primary Provider

↓

Unavailable?

↓

Fallback Provider

↓

Return
```

This improves resilience without changing application code.

---

# Queue Support

Provider synchronization can be queued.

```bash id="w6jlwm"
php artisan breach:sync --queue
```

Queued synchronization reduces request latency during large imports.

---

# Logging

Providers may emit structured log entries for:

* Request start
* Request completion
* Retry attempts
* Synchronization
* Failures

Sensitive information, including plaintext passwords and full SHA-1 hashes, must never be written to logs.

---

# Testing Providers

Provider implementations should include:

* Unit tests
* Integration tests
* Timeout tests
* Retry tests
* Error handling tests
* Parser integration tests

Use mocked PSR-18 clients for unit tests to avoid external network calls.

---

# Security

Provider implementations must:

* Never send plaintext passwords.
* Generate SHA-1 hashes locally.
* Send only the five-character SHA-1 prefix.
* Validate provider responses before parsing.
* Use HTTPS for all communication.
* Avoid logging sensitive request data.

These practices preserve the privacy guarantees of the k-Anonymity model.

---

# Best Practices

* Use the default HIBP provider unless a custom source is required.
* Configure sensible HTTP timeouts.
* Keep retry counts low.
* Enable local storage to minimize provider requests.
* Test custom providers thoroughly.
* Program against `ProviderInterface`, not concrete implementations.

---

# Future Enhancements

Potential provider capabilities include:

* Multiple provider failover
* Weighted provider selection
* Health-aware routing
* Provider metrics
* Automatic backoff
* Circuit breaker support
* Enterprise provider plugins

All future providers will remain compatible with the existing `ProviderInterface`.

---

# Next Steps

Continue with:

* **storage.md** — Learn how provider responses are persisted.
* **offline-engine.md** — Understand synchronization and local lookups.
* **database.md** — Explore the storage schema.
* **development.md** — Learn how to build custom providers.

The provider layer is intentionally lightweight, focusing solely on secure communication with external breach data services while leaving business logic, parsing, caching, and persistence to their dedicated components.
