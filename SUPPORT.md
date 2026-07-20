# Support

Thank you for using **BreachPHP**.

This document explains how to get help, report issues, request features, and contribute to the project. Following these guidelines helps maintainers respond more effectively and keeps the project organized.

---

# Documentation First

Before requesting support, please review the available documentation.

## Project Documentation

* `README.md`
* `docs/installation.md`
* `docs/quick-start.md`
* `docs/configuration.md`
* `docs/usage.md`
* `docs/api.md`
* `docs/commands.md`
* `docs/faq.md`

## Development Documentation

* `docs/architecture.md`
* `docs/project-structure.md`
* `docs/ai-rules.md`
* `docs/development.md`
* `CONTRIBUTING.md`

Many common questions are answered in these documents.

---

# Self-Diagnosis

BreachPHP includes built-in tools to help diagnose installation and configuration issues.

Verify your installation:

```bash id="j4w8qa"
php artisan breach:test
```

Run diagnostics:

```bash id="lj7oq2"
php artisan breach:doctor
```

Check local storage:

```bash id="z8xv2r"
php artisan breach:stats
```

Verify database integrity:

```bash id="qw7mp1"
php artisan breach:verify
```

These commands often identify the root cause of common issues.

---

# Bug Reports

Before opening a bug report:

* Update to the latest supported version.
* Search existing issues to avoid duplicates.
* Verify that the issue is reproducible.

Include the following information:

* BreachPHP version
* PHP version
* Laravel version (if applicable)
* Operating system
* Database driver
* Cache driver
* Steps to reproduce
* Expected behavior
* Actual behavior
* Error messages
* Relevant logs (excluding sensitive information)

A minimal reproducible example is highly encouraged.

---

# Feature Requests

Feature requests should clearly describe:

* The problem you are trying to solve
* Why the existing functionality is insufficient
* Your proposed solution
* Possible alternatives
* Any backward compatibility considerations

Constructive proposals are more likely to receive productive feedback.

---

# Security Issues

If you believe you have found a security vulnerability:

* **Do not** create a public issue.
* Follow the reporting guidance in `SECURITY.md`.
* Include enough information for maintainers to reproduce the issue safely.

Responsible disclosure helps protect all users of the project.

---

# Development Support

If you're contributing to BreachPHP, review these documents before asking implementation questions:

* `docs/architecture.md`
* `docs/ai-rules.md`
* `docs/project-structure.md`
* `docs/development.md`
* `docs/testing.md`

Understanding the project's architectural principles helps ensure consistent contributions.

---

# Common Issues

## Configuration Changes Not Applied

Clear Laravel caches:

```bash id="mpo6vy"
php artisan optimize:clear
```

---

## Provider Connectivity Problems

Run:

```bash id="8wnzv7"
php artisan breach:doctor
```

Verify:

* Internet connectivity
* HTTPS access
* Timeout configuration
* Provider availability

---

## Offline Engine Not Being Used

Verify:

* Storage driver configuration
* Database migrations
* Synchronization status
* Cache configuration

Run:

```bash id="b4v7gw"
php artisan breach:stats
```

---

## Synchronization Problems

Run:

```bash id="66rj1n"
php artisan breach:sync
```

Then verify:

```bash id="8fzj8s"
php artisan breach:verify
```

---

# Keeping Your Installation Healthy

Recommended maintenance schedule:

Daily:

```bash id="by5s8m"
php artisan breach:sync
```

Weekly:

```bash id="m63hjx"
php artisan breach:verify
```

Monthly:

```bash id="y45vow"
php artisan breach:optimize
```

Adjust the schedule based on your application's traffic and operational requirements.

---

# Before Opening an Issue

Please complete the following checklist:

* Installed the latest supported version.
* Reviewed the documentation.
* Ran `php artisan breach:test`.
* Ran `php artisan breach:doctor`.
* Confirmed the issue is reproducible.
* Removed sensitive information from logs and examples.

Completing these steps helps reduce investigation time.

---

# Best Practices

To get the most from BreachPHP:

* Keep dependencies up to date.
* Enable local storage in production when appropriate.
* Use a persistent cache such as Redis.
* Schedule synchronization and maintenance commands.
* Monitor storage growth and synchronization health.
* Read release notes before upgrading.

---

# Contributing

Community contributions are always welcome.

You can contribute by:

* Reporting bugs
* Improving documentation
* Writing tests
* Fixing issues
* Adding features
* Reviewing pull requests

Please read `CONTRIBUTING.md` before submitting code.

---

# Project Philosophy

BreachPHP aims to provide:

* A framework-independent core
* Clean Architecture
* SOLID design principles
* Privacy-preserving password breach detection
* Excellent developer experience
* Reliable production performance

Support discussions and contributions should align with these goals whenever possible.

---

# Thank You

Thank you for using and supporting BreachPHP.

Every bug report, feature request, documentation improvement, and code contribution helps make the project more reliable, secure, and useful for the PHP community.
