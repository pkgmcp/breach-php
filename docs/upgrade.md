# Upgrade Guide

This guide explains how to safely upgrade **BreachPHP** between releases.

Following these steps helps ensure a smooth upgrade while minimizing downtime and avoiding unexpected breaking changes.

---

# Versioning Policy

BreachPHP follows **Semantic Versioning (SemVer)**.

Version format:

```text id="p8qh5k"
MAJOR.MINOR.PATCH
```

Example:

```text id="n7gk1z"
1.0.0
1.1.2
1.2.5
2.0.0
```

Meaning:

| Version   | Description                                                 |
| --------- | ----------------------------------------------------------- |
| **PATCH** | Bug fixes and internal improvements. No breaking changes.   |
| **MINOR** | New features with backward compatibility.                   |
| **MAJOR** | Breaking changes or significant architectural improvements. |

---

# Before Upgrading

Always:

* Read the release notes.
* Review the changelog.
* Back up your database (if using local storage).
* Commit any uncommitted changes.
* Verify your application's test suite passes before upgrading.

---

# Update the Package

Upgrade using Composer.

```bash id="v1s5co"
composer update shamimstack/breach-php
```

To upgrade to a specific version:

```bash id="uh9o5z"
composer require shamimstack/breach-php:^1.2
```

---

# Clear Cached Files

After upgrading, clear Laravel's caches.

```bash id="f59m6u"
php artisan optimize:clear
```

Rebuild the configuration cache if applicable.

```bash id="f7bq8t"
php artisan config:cache
```

---

# Publish Updated Resources

Some releases may include updated configuration files or database migrations.

## Configuration

Republish the configuration file if instructed by the release notes.

```bash id="k0y9jt"
php artisan vendor:publish --tag=breachphp-config
```

Carefully compare the published file with your existing configuration to preserve any customizations.

---

## Migrations

Publish new migrations when available.

```bash id="1rzfqj"
php artisan vendor:publish --tag=breach-migrations
```

Run them:

```bash id="kg0lmi"
php artisan migrate
```

---

# Verify the Installation

Run the package diagnostics.

```bash id="vq4i4w"
php artisan breach:test
```

Then perform a complete health check.

```bash id="9d0zkg"
php artisan breach:doctor
```

If both commands complete successfully, the upgrade has likely been applied correctly.

---

# Upgrading Within Version 1.x

Minor and patch releases are designed to be backward compatible.

Typical changes include:

* Bug fixes
* Performance improvements
* New Artisan commands
* Additional configuration options
* Internal refactoring
* Expanded documentation

Most applications require no code changes.

---

# Upgrading to Version 2.x

Version 2 introduces major new capabilities, including support for multiple providers.

Possible breaking changes may include:

* Updated configuration structure
* New provider configuration
* Revised service registration
* New interfaces or contracts

Always review the migration guide included with the release notes before upgrading.

---

# Database Upgrades

If you use the offline storage engine:

1. Back up your database.
2. Publish any new migrations.
3. Run migrations.
4. Verify synchronization still functions correctly.

After upgrading, you may optionally run:

```bash id="00dxmo"
php artisan breach:verify
```

This validates the integrity of the local breach database.

---

# Configuration Changes

New configuration options are introduced with sensible defaults whenever possible.

Example:

```php id="jv6c0m"
'queue' => [

    'enabled' => true,

    'connection' => env('QUEUE_CONNECTION'),

],
```

When upgrading, compare your configuration file against the latest published version and add any new options that are relevant to your application.

---

# Updating Local Storage

Some releases may improve the synchronization engine or storage format.

Recommended steps:

```bash id="qu30eg"
php artisan breach:optimize

php artisan breach:stats

php artisan breach:verify
```

These commands optimize indexes, display storage statistics, and verify data consistency.

---

# Updating Tests

If your application extends BreachPHP or depends on internal contracts, run your full test suite after upgrading.

Example:

```bash id="b1r7je"
composer test
```

Any failures should be resolved before deploying to production.

---

# Common Upgrade Issues

## Configuration Not Updating

Symptoms:

* Unexpected defaults
* Missing configuration values

Solution:

```bash id="4hcg1m"
php artisan optimize:clear

php artisan config:cache
```

---

## Migration Errors

Symptoms:

* Missing tables
* Missing columns

Solution:

```bash id="mqiwgd"
php artisan migrate
```

If migrations were not published:

```bash id="bzwd8n"
php artisan vendor:publish --tag=breachphp-migrations
```

---

## Provider Connection Issues

Run:

```bash id="u9bx7z"
php artisan breach:doctor
```

Verify:

* Network connectivity
* Provider configuration
* HTTP client settings
* Timeout values

---

## Local Storage Issues

If synchronization appears inconsistent:

```bash id="2u8t91"
php artisan breach:verify
```

If necessary, rebuild your synchronized data according to the release notes.

---

# Deployment Checklist

Before deploying an upgraded version:

* Update Composer dependencies.
* Publish updated configuration (if required).
* Publish new migrations (if required).
* Run database migrations.
* Clear Laravel caches.
* Rebuild configuration cache.
* Run `breach:test`.
* Run `breach:doctor`.
* Run your application test suite.

---

# Rollback Strategy

If an upgrade introduces unexpected issues:

1. Restore your database backup.
2. Revert the Composer version.
3. Restore the previous configuration.
4. Redeploy the last known stable release.

Keeping regular backups simplifies rollback.

---

# Best Practices

* Upgrade patch releases promptly.
* Schedule minor upgrades during regular maintenance windows.
* Test major upgrades in staging before production.
* Read release notes before every upgrade.
* Keep your configuration file in version control.
* Back up local storage before schema changes.

---

# Getting Help

If you encounter problems during an upgrade:

1. Review the release notes.
2. Read the changelog.
3. Run `php artisan breach:doctor`.
4. Search the project's issue tracker.
5. Open a new issue if the problem persists, including your BreachPHP version, PHP version, Laravel version (if applicable), and any relevant error messages.

---

# Next Steps

After upgrading successfully, review:

* **usage.md** — Explore new features.
* **commands.md** — Learn about new maintenance commands.
* **offline-engine.md** — Configure any new synchronization capabilities.
* **../CHANGELOG.md** — See a detailed history of improvements.

Keeping BreachPHP up to date ensures you benefit from the latest security improvements, performance enhancements, and new functionality while maintaining a reliable password breach detection system.
