# Getting Help

This guide explains the best ways to get help when using or contributing to **BreachPHP**.

Whether you're troubleshooting an installation, reporting a bug, requesting a feature, or contributing code, following these guidelines will help resolve issues more efficiently.

---

# Before Asking for Help

Many common questions are already answered in the documentation.

Please review the following first:

* `README.md`
* `installation.md`
* `quick-start.md`
* `configuration.md`
* `api.md`
* `commands.md`
* `faq.md`

If you're developing with the package, also review:

* `architecture.md`
* `project-structure.md`
* `ai-rules.md`
* `development.md`

---

# Verify Your Installation

Before reporting an issue, ensure the package is installed correctly.

Run:

```bash id="a9szkw"
php artisan breach:test
```

If any checks fail, review the reported output before continuing.

---

# Run Diagnostics

BreachPHP includes diagnostic commands to help identify configuration problems.

```bash id="7bvy9n"
php artisan breach:doctor
```

This command verifies:

* Package configuration
* Provider connectivity
* Database configuration
* Cache configuration
* Queue configuration
* HTTP client availability

---

# Verify Local Storage

If you're using the Offline Engine:

```bash id="5ovr7m"
php artisan breach:stats
```

Check:

* Prefix count
* Suffix count
* Last synchronization
* Storage status

To verify database integrity:

```bash id="k2n0dn"
php artisan breach:verify
```

---

# Common Problems

## Package Not Found

Run:

```bash id="5mzr74"
composer install
```

or

```bash id="vprxln"
composer dump-autoload
```

---

## Configuration Not Updating

Clear Laravel caches:

```bash id="9rt0yv"
php artisan optimize:clear
```

or individually:

```bash id="v3f6pn"
php artisan config:clear

php artisan cache:clear
```

---

## Provider Connection Issues

Check:

* Internet connectivity
* HTTPS access
* Timeout configuration
* Provider availability

Then run:

```bash id="hr3h2d"
php artisan breach:doctor
```

---

## Offline Engine Not Working

Verify:

* Storage driver configuration
* Database migrations
* Synchronization status
* Cache configuration

Run:

```bash id="jlwm43"
php artisan breach:stats
```

---

## Synchronization Problems

Try:

```bash id="mw2k0r"
php artisan breach:sync
```

Then verify:

```bash id="suv4c4"
php artisan breach:verify
```

---

# Reporting a Bug

Before opening a bug report:

* Update to the latest package version.
* Confirm the issue is reproducible.
* Search existing issues to avoid duplicates.

Include:

* PHP version
* Laravel version (if applicable)
* BreachPHP version
* Operating system
* Database driver
* Cache driver
* Steps to reproduce
* Expected behavior
* Actual behavior
* Relevant error messages
* Relevant logs (excluding sensitive information)

Providing a minimal reproducible example greatly speeds up investigation.

---

# Requesting a Feature

Feature requests should explain:

* The problem you're trying to solve.
* Why the existing API is insufficient.
* Your proposed solution.
* Any backward compatibility considerations.

Where possible, include sample code or example usage.

---

# Security Issues

If you believe you have discovered a security vulnerability:

* Do **not** disclose it publicly before it has been assessed.
* Follow the project's security reporting process described in `../SECURITY.md`.
* Include enough information to reproduce and understand the issue.

Avoid posting exploit details in public issue trackers until a fix is available.

---

# Contributing

Contributions are welcome.

Before submitting code:

1. Read `../CONTRIBUTING.md`.
2. Read `development.md`.
3. Review `ai-rules.md`.
4. Ensure all tests pass.
5. Update documentation where necessary.

Every code contribution should include corresponding tests.

---

# Collect Useful Information

Useful commands:

Check installation:

```bash id="dl5l2h"
php artisan breach:test
```

Run diagnostics:

```bash id="qzc9c6"
php artisan breach:doctor
```

Show storage statistics:

```bash id="ncfqyt"
php artisan breach:stats
```

Verify storage:

```bash id="v4oh4v"
php artisan breach:verify
```

Optimize storage:

```bash id="qydk87"
php artisan breach:optimize
```

These commands provide valuable information when diagnosing issues.

---

# Debugging Tips

When troubleshooting:

* Enable application debug mode in development only.
* Check Laravel logs.
* Verify database connectivity.
* Verify cache connectivity.
* Confirm queue workers are running (if using queued synchronization).
* Reproduce the issue in a clean environment where possible.

Avoid logging plaintext passwords or other sensitive information during debugging.

---

# Keeping Your Installation Healthy

Recommended maintenance:

Daily:

```bash id="fjlwm0"
php artisan breach:sync
```

Weekly:

```bash id="pj8h1w"
php artisan breach:verify
```

Monthly:

```bash id="ntm1kq"
php artisan breach:optimize
```

Adjust this schedule based on application traffic and operational needs.

---

# Best Practices

* Keep BreachPHP updated.
* Read release notes before upgrading.
* Use the supported PHP and Laravel versions.
* Enable local storage in production if appropriate.
* Configure a persistent cache such as Redis.
* Monitor synchronization and storage growth.
* Test upgrades in a staging environment before deploying to production.

---

# Community Guidelines

When asking for help:

* Be respectful and patient.
* Provide complete technical details.
* Explain what you have already tried.
* Include only information relevant to the issue.
* Avoid sharing secrets, passwords, API keys, or sensitive user data.

Clear, concise reports make it easier for others to assist you.

---

# Additional Resources

Helpful documentation includes:

* `installation.md`
* `configuration.md`
* `api.md`
* `offline-engine.md`
* `database.md`
* `storage.md`
* `providers.md`
* `testing.md`

These guides cover most installation, configuration, development, and operational questions.

---

# Final Checklist

Before requesting support, confirm that you have:

* Installed the latest compatible version.
* Run `php artisan breach:test`.
* Run `php artisan breach:doctor`.
* Reviewed the relevant documentation.
* Collected the necessary environment information.
* Prepared clear reproduction steps.
* Removed any sensitive information from logs or examples.

Following this checklist helps ensure issues can be diagnosed and resolved as efficiently as possible.
