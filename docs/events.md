# Events

BreachPHP dispatches events throughout its lifecycle to allow applications to observe, extend, and react to password breach operations without modifying the package.

Events are entirely optional. If your application does not register listeners, the package continues to function normally.

Typical use cases include:

* Audit logging
* Metrics collection
* Notifications
* Monitoring
* Analytics
* Custom synchronization workflows

---

# Event Architecture

```text
Password Check
      │
      ▼
Password Checker
      │
      ▼
Dispatch Event
      │
 ┌────┼───────────┐
 │    │           │
 ▼    ▼           ▼
Logs Metrics Notifications
```

Events should be considered **notifications**, not extension points for changing business logic.

---

# Available Events

| Event              | Description                                        |
| ------------------ | -------------------------------------------------- |
| `PasswordChecked`  | Fired after every successful password lookup       |
| `PasswordBreached` | Fired when a password exists in breach data        |
| `PasswordSafe`     | Fired when a password is not found                 |
| `PrefixSynced`     | Fired after a prefix is synchronized successfully  |
| `SyncStarted`      | Fired before synchronization begins                |
| `SyncCompleted`    | Fired after synchronization completes              |
| `SyncFailed`       | Fired when synchronization fails                   |
| `StorageHit`       | Fired when a lookup is resolved from local storage |
| `CacheHit`         | Fired when a lookup is resolved from cache         |
| `ProviderHit`      | Fired when the configured provider is queried      |

---

# PasswordChecked

Dispatched after every successful lookup.

Typical payload:

```php
PasswordChecked
{
    result: PasswordResult
}
```

Example listener:

```php
use ShamimStack\BreachPHP\Events\PasswordChecked;

class LogPasswordChecks
{
    public function handle(PasswordChecked $event): void
    {
        logger()->info('Password checked.', [
            'source' => $event->result->source(),
        ]);
    }
}
```

---

# PasswordBreached

Dispatched when the password exists in breach data.

Payload:

```php
PasswordBreached
{
    result: PasswordResult
}
```

Example:

```php
if ($event->result->isBreached()) {
    //
}
```

Useful for:

* Security monitoring
* Audit logging
* Account security workflows

---

# PasswordSafe

Dispatched when the password is not found.

Payload:

```php
PasswordSafe
{
    result: PasswordResult
}
```

Possible uses:

* Metrics
* Reporting
* Security dashboards

---

# SyncStarted

Dispatched before synchronization begins.

Payload:

```php
SyncStarted
{
    prefix: string
}
```

Useful for:

* Progress indicators
* Logging
* Queue monitoring

---

# PrefixSynced

Dispatched after a prefix has been successfully stored.

Payload:

```php
PrefixSynced
{
    prefix: string,
    suffixCount: int
}
```

Typical uses:

* Monitoring
* Statistics
* Incremental indexing

---

# SyncCompleted

Dispatched after synchronization finishes successfully.

Payload:

```php
SyncCompleted
{
    prefix: string,
    duration: int
}
```

Useful for measuring synchronization performance.

---

# SyncFailed

Dispatched when synchronization cannot be completed.

Payload:

```php
SyncFailed
{
    prefix: string,
    exception: Throwable
}
```

Typical uses:

* Error reporting
* Retry scheduling
* Alerting

---

# StorageHit

Dispatched when a lookup is served from local storage.

Payload:

```php
StorageHit
{
    prefix: string
}
```

Useful for measuring offline lookup efficiency.

---

# CacheHit

Dispatched when a lookup is resolved directly from cache.

Payload:

```php
CacheHit
{
    key: string
}
```

Useful for cache performance metrics.

---

# ApiHit

Dispatched when BreachPHP contacts the configured provider.

Payload:

```php
ApiHit
{
    prefix: string,
    provider: string
}
```

Applications can use this event to monitor external API usage.

---

# Event Flow

A typical successful lookup:

```text
Password Checked
        │
        ▼
CacheHit? (optional)
        │
        ▼
StorageHit? (optional)
        │
        ▼
ProviderHit? (if needed)
        │
        ▼
PasswordChecked
        │
 ┌──────┴───────┐
 ▼              ▼
PasswordSafe  PasswordBreached
```

Synchronization flow:

```text
SyncStarted
      │
      ▼
Provider Request
      │
      ▼
Store Prefix
      │
      ▼
PrefixSynced
      │
      ▼
SyncCompleted
```

If synchronization fails:

```text
SyncStarted
      │
      ▼
Provider Error
      │
      ▼
SyncFailed
```

---

# Registering Listeners

Laravel example:

```php
protected $listen = [
    PasswordBreached::class => [
        LogBreachedPassword::class,
    ],
];
```

Listeners may also be registered using Laravel's event discovery if enabled.

---

# Queued Listeners

Expensive listeners should implement `ShouldQueue`.

Example:

```php
class StoreMetrics implements ShouldQueue
{
    public function handle(PasswordChecked $event): void
    {
        //
    }
}
```

Queueing listeners prevents additional latency during password checks.

---

# Event Ordering

Events are dispatched in a predictable order.

Lookup sequence:

1. `CacheHit` *(optional)*
2. `StorageHit` *(optional)*
3. `ProviderHit` *(optional)*
4. `PasswordChecked`
5. `PasswordSafe` or `PasswordBreached`

Synchronization sequence:

1. `SyncStarted`
2. `PrefixSynced`
3. `SyncCompleted`

If synchronization fails:

1. `SyncStarted`
2. `SyncFailed`

Applications should not rely on undocumented ordering beyond these guarantees.

---

# Best Practices

* Keep listeners focused on a single responsibility.
* Queue long-running listeners.
* Avoid mutating package state from listeners.
* Never throw uncaught exceptions from listeners.
* Use events for observability rather than core business logic.
* Log metadata only; never log plaintext passwords.

---

# Security

Event payloads should never expose:

* Plaintext passwords
* User credentials
* Authentication tokens
* Sensitive configuration values

Prefer working with immutable DTOs such as `PasswordResult` instead of raw request data.

---

# Future Events

Future releases may introduce additional events, including:

* `ProviderFailed`
* `ProviderRecovered`
* `CacheMiss`
* `StorageMiss`
* `DatabaseOptimized`
* `WarmupCompleted`

New events will be added in a backward-compatible manner.

---

# Next Steps

Continue with:

* **commands.md** — Learn how synchronization commands trigger events.
* **offline-engine.md** — Understand the synchronization lifecycle.
* **providers.md** — Explore provider interactions.
* **development.md** — Learn how to build custom event listeners and extensions.

The BreachPHP event system enables applications to monitor, extend, and integrate password breach operations while keeping the core package loosely coupled and maintainable.
