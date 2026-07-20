# Release Process

This document describes the official release workflow for **BreachPHP**.

The goals of the release process are to:

* Deliver stable, well-tested releases
* Maintain backward compatibility
* Follow Semantic Versioning (SemVer)
* Ensure reproducible builds
* Keep documentation synchronized with code

Every release should be predictable, automated where possible, and fully traceable.

---

# Versioning

BreachPHP follows **Semantic Versioning (SemVer)**.

```text id="t4pk7e"
MAJOR.MINOR.PATCH
```

Example:

```text id="nhwmor"
1.0.0
1.1.0
1.1.1
2.0.0
```

---

## Major Releases

Increment the major version when introducing breaking changes.

Examples:

* Public API changes
* Removed features
* Incompatible configuration changes
* Database schema changes requiring manual intervention

Example:

```text id="rzk1m5"
1.x → 2.0
```

---

## Minor Releases

Increment the minor version for backward-compatible enhancements.

Examples:

* New Artisan commands
* Additional storage drivers
* New provider implementations
* New events
* Performance improvements
* New configuration options

Example:

```text id="hjlwmn"
1.1 → 1.2
```

---

## Patch Releases

Increment the patch version for backward-compatible fixes.

Examples:

* Bug fixes
* Documentation updates
* Performance optimizations
* Security patches
* Internal refactoring

Example:

```text id="g3mrqh"
1.1.0 → 1.1.1
```

---

# Release Checklist

Before creating a release, verify:

* All tests pass.
* Static analysis passes.
* Code formatting is clean.
* Documentation is up to date.
* CHANGELOG has been updated.
* No open release blockers remain.
* CI pipeline is green.

---

# Local Verification

Run the complete verification pipeline.

```bash id="t7jlwm"
composer ci
```

This should execute:

* Formatting checks
* Static analysis
* Unit tests
* Feature tests
* Integration tests

Optionally:

```bash id="y5hd4s"
composer mutation
```

---

# Update Version

Update the package version where applicable.

Typical locations:

* `composer.json`
* `../CHANGELOG.md`
* Documentation examples (if needed)

The Git tag is considered the authoritative release version.

---

# Update Changelog

Document all user-facing changes.

Example:

```markdown id="ujlwmf"
## 1.2.0

### Added

- SQLite storage
- New health command

### Changed

- Improved synchronization performance

### Fixed

- Duplicate suffix insertion
```

Keep entries concise and user-focused.

---

# Documentation Review

Review all relevant documentation.

Typical files:

* README.md
* CHANGELOG.md
* ROADMAP.md
* API documentation
* Installation guide
* Upgrade guide

Documentation should accurately reflect the released version.

---

# Create Git Tag

Example:

```bash id="7wzq9v"
git tag v1.2.0
```

Push tags:

```bash id="u3i4wb"
git push origin --tags
```

---

# GitHub Release

Create a GitHub Release using the new tag.

Include:

* Release summary
* New features
* Bug fixes
* Upgrade notes
* Breaking changes (if any)

Attach generated release notes if available.

---

# Packagist

Packagist automatically updates when connected to the GitHub repository.

Verify that:

* The new version is available.
* Composer installation succeeds.

Example:

```bash id="dgjlwm"
composer require shamimstack/breach-php
```

---

# Post-Release Verification

After publishing:

* Install the package in a clean environment.
* Verify Laravel auto-discovery.
* Verify pure PHP installation.
* Run:

```bash id="klt5ze"
php artisan breach:test
```

Confirm that all documented installation steps remain valid.

---

# Hotfix Releases

Critical issues may require an immediate patch release.

Typical workflow:

1. Create a hotfix branch.
2. Apply the minimal fix.
3. Write regression tests.
4. Update CHANGELOG.
5. Release a new patch version.

Example:

```text id="8e4n4u"
1.2.0

↓

1.2.1
```

Avoid including unrelated changes in hotfix releases.

---

# Security Releases

For security vulnerabilities:

* Validate the issue.
* Prepare a private fix if appropriate.
* Coordinate disclosure responsibly.
* Release a patched version.
* Update `../SECURITY.md` if necessary.
* Publish advisory information when appropriate.

Security releases should be prioritized over feature work.

---

# Release Automation

The CI/CD pipeline should automate:

* Dependency installation
* Code style verification
* Static analysis
* Test execution
* Package build validation
* GitHub Release creation (optional)

Human review is still required before publishing.

---

# Release Branches

Recommended strategy:

```text id="dlfj6z"
main

↓

release/1.2

↓

tag

↓

merge
```

For smaller projects, releasing directly from the default branch after all checks pass may also be appropriate.

---

# Backward Compatibility

Within a major version:

* Preserve public APIs.
* Avoid breaking configuration.
* Avoid incompatible database changes.
* Mark deprecated functionality before removal.

Breaking changes belong only in major releases.

---

# Rollback Strategy

If a release introduces critical issues:

1. Identify the root cause.
2. Revert the affected changes if necessary.
3. Publish a patch release.
4. Communicate the issue and resolution clearly.

Do not overwrite or delete published tags.

---

# Release Frequency

Suggested cadence:

| Release Type | Frequency                                    |
| ------------ | -------------------------------------------- |
| Patch        | As needed                                    |
| Minor        | Every few weeks or when features are ready   |
| Major        | Only when justified by architectural changes |

Release based on quality, not a fixed schedule.

---

# Definition of Release Ready

A release is considered ready when:

* All CI checks pass.
* Documentation is complete.
* Changelog is accurate.
* No known critical bugs remain.
* Public APIs are stable.
* Migration and upgrade paths have been validated.

---

# Best Practices

* Keep releases small and focused.
* Automate repetitive tasks.
* Tag every published version.
* Test installation from Packagist.
* Never skip documentation updates.
* Communicate breaking changes clearly.
* Prefer frequent, incremental releases over large infrequent ones.

---

# Next Steps

Continue with:

* **upgrade.md** — Upgrade guidance for users.
* **testing.md** — Release validation and testing strategy.
* **roadmap.md** — Upcoming milestones.
* **../CHANGELOG.md** — Historical release notes.

A disciplined release process ensures that every BreachPHP version is stable, predictable, and easy for users to adopt.
