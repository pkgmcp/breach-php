# Storage

The Storage layer is responsible for persisting and retrieving password breach data used by the Offline Engine.

It provides a consistent abstraction over multiple storage backends, allowing BreachPHP to operate independently of any specific database technology.

Storage implementations **never** communicate with external providers and **never** contain business logic. Their sole responsibility is persistence.

---

# Overview

Every storage implementation must implement the `StorageInterface`.

```php id="0e6uxr"
interface StorageInterface
{
    public function hasPrefix(string $prefix): bool;

    public function find(string $prefix, string $suffix): ?int;

    public function store(PrefixResponse $response): void;

    public function deletePrefix(string $prefix): void;

    public function stats(): StorageStatistics;
}
```

This abstraction allows storage drivers to be replaced without changing the application or business logic.

---

# Storage Architecture

```text id="my8hht"
Password Checker
        │
        ▼
StorageInterface
        │
 ┌──────┼──────────────┐
 │      │              │
 ▼      ▼              ▼
Database SQLite      Redis
Storage  Storage     Storage
```

Business services depend only on the interface.

---

# Responsibilities

The storage layer is responsible for:

* Persisting synchronized prefixes
* Persisting suffixes
* Looking up stored prefixes
* Finding matching suffixes
* Returning breach counts
* Reporting storage statistics

The storage layer is **not** responsible for:

* HTTP requests
* Parsing provider responses
* Password hashing
* Validation
* Caching
* Business rules

---

# Database Storage

The default storage implementation uses a relational database.

Configuration:

```php id="1j8h6m"
'storage' => 'database',
```

Supported databases include:

* MySQL
* MariaDB
* PostgreSQL
* SQLite

Database storage is recommended for production deployments.

---

# SQLite Storage

SQLite provides a lightweight storage backend suitable for:

* Local development
* Automated testing
* Small applications
* Desktop applications

Configuration:

```php id="txg1c4"
'storage' => 'sqlite',
```

No code changes are required when switching between relational databases and SQLite.

---

# Planned Storage Drivers

Future versions may include additional storage implementations.

| Driver   | Status    |
| -------- | --------- |
| Database | Supported |
| SQLite   | Supported |
| Redis    | Planned   |
| File     | Planned   |

Each driver will implement the same interface.

---

# Lookup Flow

When checking a password:

```text id="wni2hi"
Generate SHA-1

↓

Split Hash

↓

Storage Lookup

↓

Prefix Exists?

↓

Yes

↓

Compare Suffix

↓

Return Count
```

If the prefix is missing, the Password Checker delegates to the configured provider.

---

# Store Operation

When a provider returns a prefix response:

```text id="hzlpna"
Provider Response

↓

Store Prefix

↓

Store Suffixes

↓

Commit

↓

Return
```

All write operations should occur within a database transaction where supported.

---

# Statistics

Storage implementations should expose useful operational statistics.

Example:

```php id="9lhr2d"
$stats = $storage->stats();

echo $stats->prefixes();

echo $stats->suffixes();

echo $stats->databaseSize();
```

Typical statistics include:

* Number of prefixes
* Number of suffixes
* Database size
* Last synchronization time

---

# Transactions

Database writes should be atomic.

Recommended workflow:

1. Begin transaction.
2. Insert prefix.
3. Insert suffixes.
4. Record synchronization metadata.
5. Commit transaction.

Rollback if any operation fails.

---

# Duplicate Handling

Before storing data:

* Detect existing prefixes.
* Ignore duplicate suffixes.
* Update synchronization timestamps when appropriate.

Storage drivers should avoid creating duplicate records.

---

# Error Handling

Storage drivers should throw dedicated exceptions.

Examples:

* StorageException
* ConfigurationException

Avoid throwing generic `Exception`.

---

# Performance

Recommended optimizations:

* Indexed prefix lookups
* Composite suffix indexes
* Batch inserts
* Transactions
* Prepared statements

The goal is to minimize lookup latency while supporting large synchronized datasets.

---

# Caching

Caching is handled separately from storage.

Typical lookup order:

```text id="p3tk4r"
Cache

↓

Storage

↓

Provider
```

Storage drivers should not implement caching internally.

---

# Configuration

Example:

```php id="d98d3f"
'storage' => 'database',
```

Additional driver-specific options may be introduced in future versions without changing the public API.

---

# Queue Integration

Large synchronization jobs can be processed in the background.

```bash id="scurx5"
php artisan breach:sync --queue
```

The storage layer simply persists the supplied data once the queued job reaches it.

---

# Implementing a Custom Driver

To create a custom storage backend:

1. Implement `StorageInterface`.
2. Register the driver with the package.
3. Add integration tests.
4. Document any driver-specific configuration.

Because services depend only on the interface, custom drivers require no changes to the core package.

---

# Example Custom Driver

```php id="b0n3x6"
final class CustomStorage implements StorageInterface
{
    // Implementation...
}
```

Example configuration:

```php id="2m3nqq"
'storage' => 'custom',
```

---

# Testing Storage Drivers

Every storage implementation should include:

* Unit tests
* Integration tests
* Transaction tests
* Duplicate handling tests
* Performance-oriented tests where practical

Tests should avoid external dependencies whenever possible.

---

# Best Practices

* Use relational database storage in production.
* Enable queues for large synchronization jobs.
* Use transactions for all write operations.
* Keep indexes optimized.
* Monitor storage growth.
* Perform regular integrity checks.
* Separate caching from persistence responsibilities.

---

# Security

Storage implementations must never:

* Store plaintext passwords
* Store user credentials
* Log passwords
* Expose internal secrets

Only SHA-1 prefixes, SHA-1 suffixes, and breach counts should be persisted.

---

# Future Enhancements

Potential future improvements include:

* Redis-backed storage
* Read/write replication
* Distributed storage
* Compression for suffix datasets
* Incremental synchronization metadata
* Driver-specific performance tuning

All future drivers will remain compatible with the existing `StorageInterface`.

---

# Next Steps

Continue with:

* **providers.md** — Learn how provider responses are retrieved.
* **database.md** — Explore the relational schema.
* **offline-engine.md** — Understand synchronization and offline lookups.
* **development.md** — Learn how to implement new storage drivers.

The storage layer is intentionally isolated from business logic, making it easy to optimize, replace, or extend while preserving a stable public API.
