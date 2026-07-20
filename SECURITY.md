# Security Policy

The security of **BreachPHP** and its users is a top priority.

This document explains how to report security vulnerabilities, the project's security principles, supported versions, and the security guarantees provided by the package.

---

# Supported Versions

Security updates are provided for supported release branches.

| Version | Supported |
| ------- | :-------: |
| 1.x     |     ✅     |
| 0.x     |     ❌     |
| < 1.0   |     ❌     |

Only the latest stable major version receives new features. Critical security fixes may be backported to supported versions when practical.

---

# Reporting a Vulnerability

If you believe you have discovered a security vulnerability in BreachPHP:

* **Do not** disclose it publicly before it has been assessed.
* Provide enough detail for maintainers to reproduce and understand the issue.
* Include affected versions, configuration details, and proof-of-concept code where appropriate.
* Avoid including sensitive data, production credentials, or real user information in your report.

If the project later publishes a dedicated security contact or advisory process, use that channel for all vulnerability reports.

---

# What to Include

A good security report should contain:

* BreachPHP version
* PHP version
* Laravel version (if applicable)
* Operating system
* Steps to reproduce
* Expected behavior
* Actual behavior
* Impact assessment
* Relevant logs (with sensitive information removed)

Providing a minimal reproducible example helps speed up verification and remediation.

---

# Security Response Process

The general response process is:

1. Acknowledge receipt of the report.
2. Reproduce and validate the issue.
3. Assess severity and impact.
4. Develop and test a fix.
5. Prepare a security release if required.
6. Publish release notes and any applicable advisory information.

The timeline depends on the complexity and severity of the issue.

---

# Security Principles

BreachPHP is designed around the following principles:

* Privacy by design
* Least privilege
* Defense in depth
* Secure defaults
* Explicit configuration
* Strong separation of concerns

Security considerations influence architecture, implementation, and testing decisions.

---

# Password Privacy

BreachPHP is specifically designed to protect user passwords.

The package:

* Never stores plaintext passwords.
* Never logs plaintext passwords.
* Never transmits plaintext passwords.
* Never persists user credentials.

Instead:

1. Passwords are hashed locally using SHA-1.
2. Only the first five SHA-1 characters (the prefix) are sent to the configured provider when required.
3. Suffix comparisons occur locally.

This follows the k-Anonymity protocol used by the Have I Been Pwned Pwned Passwords API.

---

# Offline Engine

When the Offline Engine is enabled, BreachPHP stores:

* SHA-1 prefixes
* SHA-1 suffixes
* Breach counts
* Synchronization metadata

The package does **not** store:

* Plaintext passwords
* User accounts
* Authentication credentials
* Password hashes used for authentication

Applications should review the provider's terms of use before retaining synchronized responses long term.

---

# Transport Security

All communication with external providers should use HTTPS.

Recommended practices include:

* TLS-enabled endpoints
* Certificate validation
* Reasonable request timeouts
* Conservative retry policies

The package should not disable TLS verification in production.

---

# Dependency Security

To reduce supply-chain risk:

* Keep dependencies up to date.
* Review dependency changes before upgrading.
* Monitor security advisories for PHP and Composer packages.
* Remove unused dependencies where possible.

Regular dependency audits are recommended.

---

# Logging

Avoid logging:

* Plaintext passwords
* Full SHA-1 hashes
* Authentication tokens
* API keys
* Secrets
* Personally identifiable information (PII)

When logging package events, prefer metadata such as:

* Provider name
* Lookup source
* Duration
* Synchronization status

---

# Secure Configuration

Production recommendations:

* Enable HTTPS for all provider communication.
* Use a persistent cache where appropriate.
* Protect database backups.
* Restrict database access using least-privilege credentials.
* Rotate secrets according to your organization's policies.

---

# Secure Development

Developers contributing to BreachPHP should:

* Write security-focused tests for new features.
* Validate external input.
* Handle exceptions safely.
* Avoid exposing internal implementation details.
* Follow secure coding practices.
* Keep documentation aligned with security behavior.

---

# Security Testing

The test suite should verify that BreachPHP:

* Never sends plaintext passwords.
* Never stores plaintext passwords.
* Correctly performs k-Anonymity lookups.
* Handles provider failures safely.
* Protects against malformed provider responses.
* Uses immutable result objects.

Security regressions should be treated as high-priority issues.

---

# Known Limitations

BreachPHP checks whether a password appears in known public breach data.

It does **not**:

* Evaluate password strength.
* Enforce password policies.
* Replace password hashing.
* Detect credential stuffing attacks.
* Replace authentication systems.

Applications should continue to use modern password hashing algorithms such as Argon2id or bcrypt for credential storage.

---

# Responsible Disclosure

Please allow maintainers reasonable time to investigate and address reported vulnerabilities before making details public.

Coordinated disclosure helps protect users while fixes are prepared and released.

---

# Security Best Practices

When integrating BreachPHP:

* Reject known breached passwords during registration and password changes.
* Combine breach checks with strong password policies.
* Use modern password hashing (e.g., Argon2id or bcrypt) for stored credentials.
* Keep BreachPHP and its dependencies updated.
* Schedule regular maintenance for local storage if using the Offline Engine.
* Avoid exposing sensitive information in logs or error messages.

---

# Credits

The privacy model implemented by BreachPHP is inspired by the **Have I Been Pwned Pwned Passwords API** and its k-Anonymity protocol, which enables password breach checks without transmitting plaintext passwords.

The project also benefits from the PHP open-source ecosystem and established PSR standards.

---

# Questions

If you have general security questions that are not vulnerability reports, please refer to the project's documentation or community support channels.

For suspected vulnerabilities, follow the reporting guidance in this document rather than opening a public issue.
