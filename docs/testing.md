# Testing

BreachPHP is designed with testability as a core architectural principle.

Every component should be independently testable through dependency injection, interfaces, immutable objects, and clear separation of responsibilities.

The project aims for **high automated test coverage** while emphasizing meaningful behavior-driven tests over raw coverage percentages.

---

# Testing Philosophy

The testing strategy is based on four principles:

* Test behavior, not implementation details.
* Keep tests fast and deterministic.
* Prefer isolated unit tests whenever possible.
* Verify complete workflows with integration and feature tests.

The package should be fully testable without requiring access to the external provider.

---

# Testing Stack

| Tool              | Purpose                 |
| ----------------- | ----------------------- |
| Pest              | Testing framework       |
| PHPUnit           | Test runner             |
| Laravel Testbench | Laravel package testing |
| Mockery           | Test doubles            |
| PHPStan           | Static analysis         |
| Infection         | Mutation testing        |
| Laravel Pint      | Code formatting         |

---

# Test Structure

```text id="h3hjlwm"
tests/
├── Unit/
├── Feature/
├── Integration/
├── Fixtures/
├── Fakes/
├── Helpers/
└── Pest.php
```

---

# Unit Tests

Unit tests verify individual classes in isolation.

Typical candidates:

* HashGenerator
* Parser
* PasswordChecker
* Value Objects
* DTOs
* Storage implementations
* Provider implementations

External dependencies should always be mocked.

Example:

```php id="l8s1f6"
it('detects a breached password', function () {
    // ...
});
```

---

# Feature Tests

Feature tests verify complete package behavior.

Examples:

* Password lookup
* Offline lookup
* Cache usage
* Artisan commands
* Validation rule
* Service container bindings
* Facade integration

Feature tests should reflect real application usage.

---

# Integration Tests

Integration tests verify interactions between multiple components.

Examples:

* Provider + Parser
* Storage + Database
* Cache + Storage
* Queue + Synchronization
* Events + Listeners

Integration tests may use an in-memory SQLite database where appropriate.

---

# Laravel Testbench

Laravel integration is tested using Laravel Testbench.

Typical responsibilities include:

* Service provider registration
* Configuration publishing
* Migration loading
* Artisan commands
* Validation rules
* Facades
* Dependency injection

---

# Mocking

Mock external systems whenever practical.

Examples:

* HTTP client
* Provider
* Cache
* Storage
* Logger

Avoid real network requests in automated tests.

---

# Fake Provider

Most tests should use a fake provider.

Example:

```php
FakeProvider
```

The fake provider returns predictable responses, enabling deterministic tests without external dependencies.

---

# Fake HTTP Client

BreachPHP includes a `FakeHttpClient` for testing HTTP-dependent components without making real network requests.

```php
use ShamimStack\BreachPHP\Http\FakeHttpClient;
use ShamimStack\BreachPHP\Http\HttpClient;
use ShamimStack\BreachPHP\Http\RequestFactory;

$fakeClient = new FakeHttpClient();
$fakeClient->setResponse(
    url: 'https://api.pwnedpasswords.com/range/5BAA6',
    body: "003D68EC5B6414BC093B9188734B9A45:3\n019B16E6B130C0C7C5F2A82D98A4E5F2:1",
    statusCode: 200
);

$httpClient = new HttpClient(
    client: $fakeClient,
    requestFactory: new RequestFactory(),
);

$provider = new HibpProvider($httpClient, new HibpParser());
```

This approach ensures tests are fast, deterministic, and do not depend on external services.

---

# Fake Storage

Testing may also use fake storage implementations.

Benefits:

* Fast execution
* No database dependency
* Predictable behavior
* Easier edge-case testing

---

# Database Testing

Database storage should be tested using:

* SQLite in memory
* Transactions
* Seeded fixtures where appropriate

Verify:

* Prefix insertion
* Suffix insertion
* Duplicate handling
* Transactions
* Integrity constraints
* Statistics

---

# Provider Testing

Provider tests should verify:

* Request generation
* Timeouts
* Retries
* Error handling
* Response parsing
* Invalid responses

HTTP clients should be mocked rather than contacting the live provider.

---

# Parser Testing

Parser tests should include:

* Valid responses
* Empty responses
* Malformed responses
* Duplicate suffixes
* Large responses

The parser should reject invalid input gracefully.

---

# Validation Testing

Laravel validation tests should verify:

* Safe passwords
* Breached passwords
* Validation messages
* FormRequest integration
* Validator integration

---

# Command Testing

Every Artisan command should be tested.

Examples:

```bash id="wuxgdk"
breach:test

breach:doctor

breach:sync

breach:verify
```

Verify:

* Exit codes
* Console output
* Error handling
* Queue dispatching
* Storage updates

---

# Event Testing

Verify:

* Correct events dispatched
* Event payloads
* Event ordering
* Queued listeners
* Failure scenarios

Laravel's event fake utilities simplify these tests.

---

# Queue Testing

Test queued synchronization by verifying:

* Jobs are dispatched
* Jobs execute successfully
* Failed jobs are handled correctly
* Retry behavior

Queue fakes should be used where appropriate.

---

# Exception Testing

Verify every package exception.

Examples:

* ApiException
* TimeoutException
* ParserException
* StorageException
* ConfigurationException

Ensure exceptions contain meaningful messages and preserve the underlying cause when appropriate.

---

# Edge Cases

Important scenarios include:

* Empty passwords
* Very long passwords
* Unicode passwords
* Duplicate prefixes
* Provider timeouts
* Corrupted storage
* Missing configuration
* Cache failures

These cases help ensure robust behavior.

---

# Performance Testing

Performance tests should measure:

* Lookup latency
* Cache hit performance
* Database lookup performance
* Synchronization speed
* Large prefix imports

Performance thresholds should be documented and monitored over time.

---

# Mutation Testing

Mutation testing helps evaluate the effectiveness of the test suite.

Run:

```bash id="yq8qpi"
composer mutation
```

Aim to eliminate surviving mutations by improving tests rather than merely increasing code coverage.

---

# Static Analysis

Run:

```bash id="7ybl2j"
composer analyse
```

Static analysis should complete without errors at the project's configured PHPStan level.

---

# Code Style

Run:

```bash id="q8cjlwm"
composer format
```

All code should comply with the project's Laravel Pint configuration before merging.

---

# Continuous Integration

Every pull request should execute:

1. Dependency installation
2. Code formatting checks
3. Static analysis
4. Unit tests
5. Feature tests
6. Integration tests
7. Mutation testing (where configured)

No pull request should be merged if required checks fail.

---

# Test Coverage

Coverage targets are guidelines rather than goals in themselves.

Recommended minimums:

| Area      | Target |
| --------- | -----: |
| Domain    |   100% |
| Services  |    95% |
| Storage   |    95% |
| Providers |    95% |
| Commands  |    90% |
| Events    |    90% |
| Overall   |   90%+ |

Well-designed tests are more valuable than achieving a specific percentage.

---

# Best Practices

* Write tests before fixing bugs where practical.
* Keep each test focused on one behavior.
* Prefer descriptive test names.
* Avoid shared mutable state.
* Use fakes instead of real services.
* Mock only external boundaries.
* Keep tests independent and repeatable.
* Refactor tests as the codebase evolves.

---

# Security Testing

Verify that the package never:

* Sends plaintext passwords to a provider.
* Stores plaintext passwords.
* Logs plaintext passwords.
* Exposes sensitive configuration values.

Security-focused tests should remain part of the regular test suite.

---

# Next Steps

Continue with:

* **development.md** — Learn the contributor workflow.
* **api.md** — Explore the public API under test.
* **providers.md** — Review provider implementation details.
* **storage.md** — Understand storage behavior and persistence.

A comprehensive, reliable test suite is essential to ensuring BreachPHP remains secure, maintainable, and dependable across future releases.
