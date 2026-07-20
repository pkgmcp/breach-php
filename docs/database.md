# Database

The database layer is responsible for storing synchronized password breach data used by the **Offline Engine**.

BreachPHP stores **SHA-1 hash prefixes and suffixes**, never plaintext passwords.

The schema is designed for:

* Fast lookups
* Low memory usage
* Efficient synchronization
* Scalability
* Database portability

---

# Overview

The Offline Engine stores provider responses using three tables.

```text id="jlwmgg"
prefixes

suffixes

sync_logs
```

Relationships

```text id="zqg0nx"
prefixes

    │

    ├──────────────┐

    ▼              │

suffixes           │

                   ▼

             sync_logs
```

---

# prefixes Table

Stores every synchronized SHA-1 prefix.

## Columns

| Column     | Type      | Description          |
| ---------- | --------- | -------------------- |
| id         | bigint    | Primary key          |
| prefix     | char(5)   | SHA-1 prefix         |
| synced_at  | timestamp | Last synchronization |
| created_at | timestamp | Record creation      |
| updated_at | timestamp | Last update          |

---

## Example

| id | prefix  |
| -: | ------- |
|  1 | `CBFDA` |
|  2 | `A94A8` |

---

## Indexes

```text id="wrdn2h"
PRIMARY KEY(id)

UNIQUE(prefix)
```

Only one record exists per prefix.

---

# suffixes Table

Stores all suffixes belonging to a prefix.

## Columns

| Column     | Type      | Description                |
| ---------- | --------- | -------------------------- |
| id         | bigint    | Primary key                |
| prefix_id  | bigint    | Related prefix             |
| suffix     | char(35)  | Remaining SHA-1 characters |
| count      | bigint    | Number of known breaches   |
| created_at | timestamp | Record creation            |
| updated_at | timestamp | Last update                |

---

## Example

| prefix_id | suffix                                |  count |
| --------- | ------------------------------------- | -----: |
| 1         | `C6008F9CAB4083784CBD1874F76618D2A97` | 124532 |
| 1         | `72A4DAB6F...`                        |     15 |

---

## Indexes

```text id="rn7apz"
PRIMARY KEY(id)

INDEX(prefix_id)

UNIQUE(prefix_id, suffix)
```

This composite index enables efficient lookups by prefix and suffix.

---

# sync_logs Table

Stores synchronization history.

This table is optional but recommended for monitoring and troubleshooting.

## Columns

| Column      | Type      | Description             |
| ----------- | --------- | ----------------------- |
| id          | bigint    | Primary key             |
| prefix      | char(5)   | Synchronized prefix     |
| status      | string    | Sync status             |
| started_at  | timestamp | Start time              |
| finished_at | timestamp | Completion time         |
| duration    | integer   | Duration (milliseconds) |
| error       | text      | Error details, if any   |
| created_at  | timestamp | Record creation         |

---

## Example

| prefix  | status  | duration |
| ------- | ------- | -------: |
| `CBFDA` | success |      284 |
| `A94A8` | failed  |     5012 |

---

# Relationships

```text id="k4n05h"
Prefix

1

↓

Many

Suffixes
```

Each prefix can contain thousands of suffixes.

---

# Lookup Process

Password

↓

Generate SHA-1

↓

Split Hash

↓

Prefix

↓

Database Lookup

↓

Suffix Match

↓

Return Count

The database never stores plaintext passwords.

---

# Example Lookup

Password

```text id="ewg8n5"
password123
```

SHA-1

```text id="k1muh0"
CBFDAC6008F9CAB4083784CBD1874F76618D2A97
```

Split

```text id="r6wjlm"
Prefix

CBFDA

Suffix

C6008F9CAB4083784CBD1874F76618D2A97
```

Query

```sql id="yhc6w4"
SELECT count
FROM suffixes
WHERE prefix_id = ?
AND suffix = ?;
```

If a row exists, the password has appeared in known breaches.

---

# Supported Databases

Supported

* SQLite
* MySQL
* MariaDB
* PostgreSQL

Future

* Redis Storage
* File Storage

Every storage driver implements `StorageInterface`.

---

# Migrations

Publish package migrations.

```bash id="szy6wq"
php artisan vendor:publish --tag=breach-migrations
```

Run migrations.

```bash id="l3z7tp"
php artisan migrate
```

---

# Synchronization

When a prefix is not found locally:

1. Query the configured provider.
2. Parse the response.
3. Insert the prefix.
4. Insert all returned suffixes.
5. Record synchronization metadata.
6. Return the lookup result.

Future requests for the same prefix are served locally.

---

# Transactions

Database writes should occur within transactions.

Example workflow:

1. Insert prefix.
2. Insert suffixes.
3. Write synchronization log.
4. Commit.

Rollback if any step fails.

This ensures consistency between related tables.

---

# Indexing Strategy

Recommended indexes:

| Table     | Index                       |
| --------- | --------------------------- |
| prefixes  | `UNIQUE(prefix)`            |
| suffixes  | `INDEX(prefix_id)`          |
| suffixes  | `UNIQUE(prefix_id, suffix)` |
| sync_logs | `INDEX(prefix)`             |
| sync_logs | `INDEX(status)`             |

Proper indexing is essential for lookup performance.

---

# Storage Growth

The local database grows as additional prefixes are synchronized.

Growth depends on:

* Number of unique prefixes requested
* Warmup strategy
* Manual synchronization
* Application traffic

Not every possible prefix will necessarily be stored.

---

# Maintenance

Useful Artisan commands:

Synchronize

```bash id="lzdg4d"
php artisan breach:sync
```

Verify

```bash id="tfdhmz"
php artisan breach:verify
```

Optimize

```bash id="v7nv6i"
php artisan breach:optimize
```

Statistics

```bash id="cfmhgi"
php artisan breach:stats
```

Prune

```bash id="g9d2nh"
php artisan breach:prune
```

---

# Backup Strategy

If local storage is important to your deployment:

* Include the BreachPHP tables in regular database backups.
* Test restore procedures periodically.
* Back up before major package upgrades or schema changes.

---

# Performance Considerations

Recommended for production:

* Use indexed tables.
* Keep statistics up to date.
* Use Redis or another persistent cache.
* Enable queue-based synchronization.
* Optimize the database after large synchronization jobs.

---

# Security

The database stores:

* SHA-1 prefixes
* SHA-1 suffixes
* Breach counts

The database never stores:

* Plaintext passwords
* User credentials
* Authentication tokens

Only derived hash information is persisted.

---

# Limitations

The database represents an incremental local mirror of synchronized provider responses.

It does **not** contain every possible SHA-1 prefix unless you intentionally synchronize the complete dataset in accordance with the provider's licensing and usage policies.

Applications without a locally stored prefix will continue to rely on the configured provider for that lookup.

---

# Best Practices

* Enable local storage in production.
* Use transactions for synchronization.
* Schedule regular integrity checks.
* Monitor table growth.
* Periodically optimize indexes.
* Keep database backups.
* Review provider terms before retaining synchronized data long term.

---

# Next Steps

Continue with:

* **storage.md** — Learn how storage drivers implement this schema.
* **offline-engine.md** — Understand the synchronization workflow.
* **commands.md** — Automate synchronization and maintenance.
* **providers.md** — Learn how external provider responses are processed.

The database schema is intentionally simple, portable, and optimized for fast password breach lookups while preserving the privacy guarantees of the k-Anonymity protocol.
