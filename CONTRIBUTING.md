# Contributing to BreachPHP

First, thank you for your interest in contributing to **BreachPHP**.

Whether you're reporting a bug, improving documentation, adding tests, fixing an issue, or implementing a new feature, your contribution is appreciated.

This document explains the project's development workflow, coding standards, and expectations for contributors.

---

# Code of Conduct

Contributors are expected to:

* Be respectful and constructive.
* Welcome feedback.
* Focus on technical discussions.
* Help maintain a friendly and inclusive community.
* Respect differing viewpoints and experiences.

Harassment, discrimination, or abusive behavior will not be tolerated.

---

# Ways to Contribute

You can contribute by:

* Reporting bugs
* Improving documentation
* Writing or improving tests
* Fixing bugs
* Adding new features
* Improving performance
* Reviewing pull requests
* Suggesting architectural improvements

Not every contribution needs to involve code.

---

# Before You Start

Before making significant changes, please:

1. Review the documentation.
2. Search for existing issues or discussions.
3. Confirm that the work is not already in progress.
4. Open an issue for large features or architectural changes.

This helps avoid duplicated effort and encourages early feedback.

---

# Development Environment

Requirements:

* PHP 8.4+
* Composer 2.x
* Git
* SQLite (recommended for local testing)

Clone the repository:

```bash id="srm3i2"
git clone https://github.com/shamimstack/breach-php.git

cd breach-php
```

Install dependencies:

```bash id="zwkxjv"
composer install
```

Run the test suite:

```bash id="wb6tqv"
composer test
```

---

# Development Workflow

Typical workflow:

1. Create a feature branch.
2. Implement the change.
3. Add or update tests.
4. Update documentation if needed.
5. Run the full quality pipeline.
6. Submit a pull request.

Keep pull requests focused on a single topic whenever possible.

---

# Branch Naming

Recommended examples:

```text id="4s3ywk"
feature/offline-storage

feature/sqlite-driver

fix/parser-error

fix/provider-timeout

docs/api-reference

refactor/password-checker
```

Choose names that clearly describe the purpose of the branch.

---

# Coding Standards

BreachPHP follows:

* PSR-12
* PSR-4
* SOLID principles
* Clean Architecture

Additional expectations:

* `declare(strict_types=1);`
* Typed properties
* Constructor property promotion
* Readonly properties where appropriate
* Explicit return types
* Small, focused classes
* Constructor dependency injection

Avoid introducing unnecessary complexity.

---

# Architecture

The project separates responsibilities into distinct layers.

Core:

* Contracts
* DTOs
* Value Objects
* Domain Services

Infrastructure:

* Providers
* Storage
* Cache
* Console Commands

Laravel Integration:

* Service Provider
* Validation Rule
* Facade
* Configuration

Framework-specific code should remain outside the core domain.

---

# Testing

Every contribution should include appropriate tests.

Run:

```bash id="x9w2j5"
composer test
```

Static analysis:

```bash id="lhvmye"
composer analyse
```

Formatting:

```bash id="fljh9j"
composer format
```

Mutation testing:

```bash id="d8h7xk"
composer mutation
```

No pull request should reduce test quality.

---

# Documentation

Documentation is considered part of the codebase.

Update documentation whenever:

* Public APIs change
* Configuration changes
* Commands change
* Architecture changes
* New features are added

Typical files include:

* README.md
* API reference
* Configuration guide
* Commands guide
* Upgrade guide
* CHANGELOG.md

---

# Commit Messages

Recommended format:

```text id="5bw1zc"
feat: add sqlite storage

fix: prevent duplicate suffixes

docs: improve installation guide

test: add provider integration tests

refactor: simplify parser
```

Follow Conventional Commits where practical.

---

# Pull Requests

A pull request should:

* Solve one problem.
* Pass all CI checks.
* Include tests.
* Include documentation updates where applicable.
* Preserve backward compatibility unless intentionally introducing a major-version change.

Small pull requests are easier to review and merge.

---

# Code Review

Reviewers may request:

* Additional tests
* Documentation updates
* Refactoring
* Naming improvements
* Performance improvements
* Architectural adjustments

Code reviews are intended to improve quality, not criticize contributors.

---

# Bug Reports

A good bug report includes:

* PHP version
* Laravel version (if applicable)
* Package version
* Steps to reproduce
* Expected behavior
* Actual behavior
* Relevant logs (excluding sensitive information)

Whenever possible, include a minimal reproducible example.

---

# Feature Requests

Feature proposals should explain:

* The problem being solved
* The proposed solution
* Alternative approaches considered
* Backward compatibility impact

Large features should generally be discussed before implementation.

---

# Security

If you discover a security vulnerability:

* Do **not** open a public issue.
* Follow the guidance in `SECURITY.md`.
* Provide enough information for maintainers to reproduce the issue.

Responsible disclosure helps protect users.

---

# Definition of Done

A contribution is considered complete when:

* The implementation is finished.
* Tests pass.
* Static analysis passes.
* Code formatting passes.
* Documentation is updated.
* CI succeeds.
* The change has been reviewed.

---

# Best Practices

* Prefer interfaces over concrete implementations.
* Keep classes focused.
* Avoid unnecessary dependencies.
* Use immutable DTOs and Value Objects.
* Write clear commit messages.
* Leave the codebase cleaner than you found it.
* Prioritize readability and maintainability.

---

# Recognition

Every contribution—whether code, documentation, testing, or feedback—helps improve BreachPHP.

Thank you for taking the time to contribute and help make the project more secure, maintainable, and useful for the PHP community.
