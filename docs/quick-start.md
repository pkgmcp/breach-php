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

```php id="pcv7jg"
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

```php id="1n7hlq"
$result = BreachPHP::check('password123');

$result->isBreached(); // bool

$result->count(); // int
```

Typical API:

```php id="c08uzx"
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

```php id="b5eq0u"
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

```php id="rye9hm"
$result = BreachPHP::check($password);

if ($result->isBreached()) {
    // Handle compromised password
}
```

---

# Using Dependency Injection

For larger applications, dependency injection is recommended over facades.

```php id="f9kl6w"
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

# Using Local Storage

Enable local storage in your configuration.

```php id="2ofg4m"
'storage' => 'database',
```

Run the package migrations.

```bash id="8gbnrl"
php artisan migrate
```

Synchronize breach prefixes.

```bash id="f2a95t"
php artisan breach:sync
```

When a password is checked:

1. The local database is searched.
2. If the prefix exists, the result is returned immediately.
3. If the prefix is missing, BreachPHP queries the provider, stores the prefix response, and returns the result.

Over time, the local database grows and reduces the number of external API requests.

---

# Using Cache

Caching reduces repeated lookups for the same prefix.

Example configuration:

```php id="oh80az"
'cache' => 'redis',
```

Supported cache drivers include:

* Laravel Cache
* PSR-16 Cache
* Redis
* Array

---

# Running Diagnostics

Verify that your installation is working correctly.

```bash id="5nt7d3"
php artisan breach:test
```

Run a full system check.

```bash id="b5sm4d"
php artisan breach:doctor
```

View package statistics.

```bash id="s90vdo"
php artisan breach:stats
```

---

# Handling Errors

Catch package-specific exceptions when you need custom error handling.

```php id="b5gx83"
use ShamimStack\BreachPHP\Exceptions\ApiException;

try {
    $result = BreachPHP::check($password);
} catch (ApiException $e) {
    // Handle provider failure
}
```

Avoid catching generic `Exception` where a more specific exception is appropriate.

---

# Typical Registration Example

```php id="g9n1my"
$password = $request->string('password');

$result = BreachPHP::check($password);

if ($result->isBreached()) {
    return back()->withErrors([
        'password' => 'This password has appeared in known data breaches. Please choose a different one.',
    ]);
}

// Continue creating the user...
```

---

# What Happens Behind the Scenes?

When you call:

```php id="ovlg1d"
BreachPHP::check($password);
```

The package performs the following steps:

1. Generates the SHA-1 hash locally.
2. Splits the hash into a five-character prefix and a suffix.
3. Checks local storage (if enabled).
4. Queries the configured provider if necessary.
5. Parses the provider response.
6. Stores the prefix locally (when configured).
7. Returns an immutable result object.

At no point is the plaintext password transmitted to the provider.

---

# Next Steps

Now that you've completed the quick start, explore the rest of the documentation:

* **configuration.md** — Configure providers, storage, cache, and timeouts.
* **usage.md** — Learn the full public API.
* **validation.md** — Integrate with Laravel validation.
* **offline-engine.md** — Build a local breach database.
* **commands.md** — Discover all available Artisan commands.

You're now ready to use BreachPHP in production-ready PHP and Laravel applications.
