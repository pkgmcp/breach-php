# Quick Start

This guide walks you through the most common BreachPHP features in just a few minutes.

By the end of this guide, you'll be able to:

* Check whether a password has been exposed in known data breaches.
* Retrieve the breach count.
* Use BreachPHP in Laravel and plain PHP.
* Validate passwords in Laravel.
* Enable local storage for faster lookups.

---

# Before You Begin

Ensure you have:

* Installed BreachPHP
* Published the configuration (Laravel)
* Completed the installation guide

If you haven't installed the package yet, see **installation.md**.

---

# Your First Password Check

## Laravel

```php
use ShamimStack\BreachPHP\Facades\BreachPHP;

$result = BreachPHP::check('password123');

if ($result->isBreached()) {
    echo "Password found {$result->count()} times.";
} else {
    echo "Password is not known to be breached.";
}
```

---

## Pure PHP

```php
$result = breach_check('password123');

if ($result->isBreached()) {
    echo $result->count();
}
```

---

# Understanding the Result

The `check()` method returns an immutable result object.

Example:

```php
$result = BreachPHP::check('password123');

$result->isBreached(); // bool

$result->count(); // int
```

Typical API:

```php
$result->isBreached();

$result->count();

$result->hash();

$result->prefix();

$result->suffix();
```

Because the object is immutable, its state cannot be modified after creation.

---

# Laravel Validation

Prevent users from choosing compromised passwords.

```php
use Illuminate\Support\Facades\Validator;
use ShamimStack\BreachPHP\Rules\NotBreached;

Validator::make($request->all(), [
    'password' => [
        'required',
        'string',
        'min:12',
        new NotBreached(),
    ],
])->validate();
```

If the password appears in a known breach, validation fails automatically.

---

# Using the Facade

The Laravel facade provides a clean and expressive API.

```php
$result = BreachPHP::check($password);

if ($result->isBreached()) {
    // Handle compromised password
}
```

---

# Using Dependency Injection

For larger applications, dependency injection is recommended over facades.

```php
use ShamimStack\BreachPHP\Contracts\PasswordCheckerInterface;

final class RegistrationService
{
    public function __construct(
        private readonly PasswordCheckerInterface $checker,
    ) {}

    public function validate(string $password): void
    {
        $result = $this->checker->check($password);

        if ($result->isBreached()) {
            // Reject password
        }
    }
}
```

---

# Next Steps

Now that you've completed the quick start, explore the rest of the documentation:

* **configuration.md** — Configure providers, storage, cache, and timeouts.
* **usage.md** — Learn the full public API.
* **validation.md** — Integrate with Laravel validation.
* **offline-engine.md** — Build a local breach database.
* **commands.md** — Discover all available Artisan commands.

You're now ready to use BreachPHP in production-ready PHP and Laravel applications.
