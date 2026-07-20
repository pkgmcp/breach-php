# Offline Engine

The **Offline Engine** is one of BreachPHP's core features.

It allows applications to gradually build a local password breach database by storing synchronized responses from the configured provider. Over time, this reduces external API requests, improves lookup performance, and enables continued operation when previously synchronized data is available.

---

# Why an Offline Engine?

Without local storage, every password check requires a request to the configured provider.

```
Password
    │
    ▼
Provider API
    │
    ▼
Result
```

While this approach is simple, it depends on network connectivity and provider availability.

The Offline Engine adds a local lookup layer.

```
Password
    │
    ▼
Local Storage
    │
 ┌──┴──┐
 │     │
Hit   Miss
 │     │
 ▼     ▼
Result Provider API
          │
          ▼
    Store Prefix
          │
          ▼
        Result
```

As more prefixes are synchronized, the percentage of local lookups increases.

---

# How It Works

BreachPHP uses the same **k-Anonymity** model as the Have I Been Pwned Pwned Passwords API.

For every password:

1. Generate the SHA-1 hash locally.
2. Split the hash into:

   * a **5-character prefix**
   * a **35-character suffix**
3. Search the local database for the prefix.
4. If found, compare the suffix locally.
5. If not found, query the configured provider.
6. Store the complete prefix response locally.
7. Return an immutable result object.

Only the five-character prefix is transmitted to the provider. The plaintext password never leaves your application.

---

# Example

Password

```
password123
```

SHA-1

```
CBFDAC6008F9CAB4083784CBD1874F76618D2A97
```

Split into

```
Prefix

CBFDA

Suffix

C6008F9CAB4083784CBD1874F76618D2A97
```

The provider receives only:

```
CBFDA
```

The returned suffix list is stored locally.

Future passwords with the same prefix require no additional provider request.

---

# Local Database

The Offline Engine stores prefix responses using two tables.

## prefixes

| Column     | Description                 |
| ---------- | --------------------------- |
| id         | Primary key                 |
| prefix     | Five-character SHA-1 prefix |
| synced_at  | Last synchronization time   |
| created_at | Record creation timestamp   |
| updated_at | Last update timestamp       |

---

## suffixes

| Column     | Description               |
| ---------- | ------------------------- |
| id         | Primary key               |
| prefix_id  | Related prefix            |
| suffix     | SHA-1 suffix              |
| count      | Breach count              |
| created_at | Record creation timestamp |
| updated_at | Last update timestamp     |

Indexes should be created on:

* `prefix`
* `(prefix_id, suffix)`

This enables fast local lookups.

---

# Lookup Flow

```
Password

↓

Generate SHA-1

↓

Split Hash

↓

Find Prefix

↓

Prefix Exists?

 ┌──────────────┐
 │              │
Yes            No
 │              │
 ▼              ▼
Compare     Query Provider
Suffix           │
 │               ▼
 ▼          Store Prefix
 │               │
 └──────► Return Result
```

---

# Synchronization Strategies

## Automatic

Whenever a missing prefix is encountered:

1. Query the provider.
2. Store the complete prefix.
3. Return the result.

This gradually builds the local database with no manual intervention.

---

## Manual

Synchronize prefixes explicitly.

```bash
php artisan breach:sync
```

Useful for maintenance or targeted synchronization.

---

## Warmup

Download commonly used prefixes before the application begins handling requests.

```bash
php artisan breach:warmup
```

This reduces initial provider requests after deployment.

---

# Cache Layer

A cache layer sits in front of storage.

```
Password

↓

Cache

↓

Database

↓

Provider
```

Benefits:

* Fewer database queries
* Lower latency
* Better scalability

Recommended production cache:

```
Redis
```

---

# Offline Mode

If the provider is unavailable:

```
Password

↓

Local Storage

↓

Result
```

The Offline Engine can only answer lookups for prefixes that have already been synchronized.

If a prefix has never been stored locally, a remote lookup cannot be completed until the provider becomes available again.

---

# Queue Support

Synchronization jobs can run in the background.

```
Password

↓

Queue

↓

Synchronization

↓

Database
```

Example:

```bash
php artisan breach:sync --queue
```

Large synchronization jobs should use queues in production.

---

# Storage Drivers

Supported

| Driver   | Status    |
| -------- | --------- |
| Database | Supported |
| SQLite   | Supported |
| Redis    | Planned   |
| File     | Planned   |

Every driver implements `StorageInterface`.

---

# Performance

The Offline Engine significantly reduces network traffic after prefixes have been synchronized.

Typical workflow:

```
First Lookup

Password

↓

Provider

↓

Store Prefix

↓

Return
```

Later lookups:

```
Password

↓

Local Database

↓

Return
```

This generally provides:

* Lower latency
* Fewer HTTP requests
* Better resilience during provider outages
* Reduced dependency on external services

Actual performance depends on the percentage of requested prefixes already stored locally.

---

# Data Integrity

Use the verification command regularly.

```bash
php artisan breach:verify
```

Checks include:

* Missing prefixes
* Duplicate suffixes
* Invalid relationships
* Database integrity

---

# Maintenance

Recommended schedule:

Daily

```bash
php artisan breach:sync
```

Weekly

```bash
php artisan breach:verify
```

Monthly

```bash
php artisan breach:optimize
```

Adjust the schedule based on your application's traffic and operational needs.

---

# Best Practices

* Enable local storage in production.
* Use Redis or another persistent cache.
* Queue synchronization jobs.
* Verify storage integrity regularly.
* Optimize the database after large synchronization operations.
* Monitor storage growth and available disk space.
* Review the provider's terms of use before storing synchronized responses long term.

---

# Limitations

The Offline Engine is an **incremental local mirror**, not a complete copy of the provider's database.

This means:

* Only synchronized prefixes are available offline.
* New prefixes still require a provider lookup.
* Synchronization is demand-driven unless proactively warmed or synchronized.

Applications requiring complete offline coverage would need to populate all prefixes in accordance with the provider's licensing and usage policies.

---

# Security

The Offline Engine follows the same security principles as online lookups:

* Plaintext passwords are never stored.
* Plaintext passwords are never transmitted.
* SHA-1 hashes are generated locally.
* Only the five-character prefix is sent to the provider when required.
* Stored data consists of SHA-1 suffixes and breach counts only.

---

# Next Steps

Continue with:

* **database.md** — Learn the storage schema in detail.
* **storage.md** — Understand storage driver implementations.
* **providers.md** — Explore provider integrations.
* **commands.md** — Learn how to synchronize and maintain local storage.

The Offline Engine is designed to improve performance, resilience, and scalability while preserving the privacy guarantees of the k-Anonymity model.
