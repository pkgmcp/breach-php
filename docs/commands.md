# Commands

BreachPHP includes a collection of Artisan commands for checking passwords, synchronizing local storage, monitoring system health, and maintaining the package.

You can view all available commands at any time:

```bash
php artisan list breach
```

---

# Available Commands

| Command              | Description                                     |
| -------------------- | ----------------------------------------------- |
| `breach:check`       | Check whether a password has been breached      |
| `breach:sync`        | Synchronize breach prefixes from the provider   |
| `breach:warmup`      | Preload frequently used prefixes                |
| `breach:test`        | Verify package installation                     |
| `breach:doctor`      | Run diagnostics and detect configuration issues |
| `breach:health`      | Display package health information              |
| `breach:stats`       | Show storage and synchronization statistics     |
| `breach:verify`      | Verify the integrity of local storage           |
| `breach:cache-clear` | Clear the package cache                         |
| `breach:prune`       | Remove obsolete or invalid records              |
| `breach:optimize`    | Optimize storage and indexes                    |

---

# breach:check

Checks whether a password appears in known breach data.

```bash
php artisan breach:check
```

Interactive example:

```text
Enter password:
**************

Checking...

✔ Password checked

Status : Breached
Count  : 124,532
Source : Have I Been Pwned
```

Options:

| Option        | Description                                                                                                                              |
| ------------- | ---------------------------------------------------------------------------------------------------------------------------------------- |
| `--password=` | Provide the password without an interactive prompt *(not recommended for production environments because command history may expose it)* |
| `--json`      | Return JSON output                                                                                                                       |
| `--offline`   | Force local storage lookup only                                                                                                          |

---

# breach:sync

Synchronizes prefix data from the configured provider into local storage.

```bash
php artisan breach:sync
```

Example:

```text
Synchronizing...

Processed Prefixes : 250

Stored Prefixes    : 250

Stored Suffixes    : 12,456

Completed
```

Options:

| Option      | Description                                |
| ----------- | ------------------------------------------ |
| `--prefix=` | Synchronize a specific SHA-1 prefix        |
| `--queue`   | Dispatch synchronization jobs to the queue |
| `--force`   | Re-download existing prefixes              |

---

# breach:warmup

Downloads frequently requested prefixes to improve future lookup performance.

```bash
php artisan breach:warmup
```

Example:

```text
Warmup started...

Downloading common prefixes...

Completed.
```

Warmup is useful immediately after installation or before handling large volumes of password checks.

---

# breach:test

Verifies that BreachPHP is installed and configured correctly.

```bash
php artisan breach:test
```

Typical output:

```text
✔ Package installed

✔ Configuration loaded

✔ HTTP client available

✔ Storage configured

✔ Cache configured

✔ Provider reachable

✔ Installation successful
```

Run this command after installation or upgrades.

---

# breach:doctor

Runs a comprehensive diagnostics check.

```bash
php artisan breach:doctor
```

Example output:

```text
Configuration      ✔

Provider           ✔

Database           ✔

Cache              ✔

HTTP Client        ✔

Network            ✔

Queue              ✔

Environment        ✔

No issues detected.
```

If issues are found, the command provides suggested solutions.

---

# breach:health

Displays the operational health of the package.

```bash
php artisan breach:health
```

Example:

```text
Provider Status      Healthy

Storage              Healthy

Cache                Healthy

Synchronization      Healthy

Overall Status       Healthy
```

This command is useful for monitoring and automation.

---

# breach:stats

Displays statistics about local storage.

```bash
php artisan breach:stats
```

Example:

```text
Prefixes Stored     14,832

Suffixes Stored     8,451,291

Database Size       326 MB

Cache Driver        Redis

Last Sync           2026-07-19 10:15 UTC
```

The reported values depend on your configured storage driver.

---

# breach:verify

Verifies the integrity of the local storage.

```bash
php artisan breach:verify
```

Checks include:

* Missing prefixes
* Invalid suffix records
* Duplicate entries
* Broken relationships
* Index validation

Example:

```text
Checking database...

✔ Prefixes

✔ Suffixes

✔ Indexes

✔ Integrity verified
```

---

# breach:cache-clear

Clears the package cache.

```bash
php artisan breach:cache-clear
```

Typical output:

```text
Package cache cleared successfully.
```

This command does not affect synchronized database records.

---

# breach:prune

Removes obsolete or invalid data.

```bash
php artisan breach:prune
```

Example:

```text
Removed:

• Invalid prefixes

• Duplicate suffixes

• Orphaned records

Database cleanup complete.
```

Run periodically as part of routine maintenance.

---

# breach:optimize

Optimizes the local storage.

```bash
php artisan breach:optimize
```

Example:

```text
Optimizing...

✔ Database indexes

✔ Statistics

✔ Cache

✔ Storage

Optimization complete.
```

Recommended after large synchronization operations.

---

# Scheduling Commands

Several commands can be scheduled using Laravel's scheduler.

Example:

```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('breach:sync')->daily();

Schedule::command('breach:verify')->weekly();

Schedule::command('breach:optimize')->weekly();

Schedule::command('breach:prune')->monthly();
```

Adjust the schedule based on your application's usage and operational requirements.

---

# Queue Support

Long-running commands can dispatch work to Laravel queues.

Example:

```bash
php artisan breach:sync --queue
```

Run a queue worker:

```bash
php artisan queue:work
```

Using queues prevents lengthy synchronization tasks from blocking CLI execution.

---

# Exit Codes

Commands should return standard exit codes.

| Code | Meaning              |
| ---: | -------------------- |
|  `0` | Success              |
|  `1` | General failure      |
|  `2` | Configuration error  |
|  `3` | Provider unavailable |
|  `4` | Storage error        |

These codes simplify automation and monitoring.

---

# Automation

Example cron entry:

```bash
0 2 * * * php artisan breach:sync --quiet

0 3 * * 0 php artisan breach:verify --quiet

0 4 1 * * php artisan breach:optimize --quiet
```

Alternatively, use Laravel's scheduler for framework-based applications.

---

# Best Practices

* Run `breach:test` after installation and upgrades.
* Use `breach:doctor` when troubleshooting configuration or connectivity issues.
* Schedule `breach:sync` if local storage is enabled.
* Verify storage integrity regularly.
* Optimize the database after large synchronization jobs.
* Prefer queued synchronization for production deployments.
* Avoid passing passwords through command-line options on shared systems.

---

# Next Steps

Continue with:

* **offline-engine.md** — Understand how BreachPHP builds and uses a local breach database.
* **database.md** — Learn the storage schema and indexing strategy.
* **api.md** — Explore the complete public API reference.

These commands provide the operational tooling needed to install, monitor, maintain, and scale BreachPHP in production environments.
