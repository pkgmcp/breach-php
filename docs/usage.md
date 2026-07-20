# Usage

This guide covers the public API of **BreachPHP** and demonstrates how to use the package in Laravel and framework-agnostic PHP applications.

The API is intentionally simple while the underlying implementation remains modular and extensible.

---

# Basic Password Check

The most common operation is checking whether a password has appeared in known data breaches.

## Laravel

```php
use ShamimStack\BreachPHP\Facades\BreachPHP;

$result = BreachPHP::check('password123');

if ($result->isBreached()) {
    echo "Password has been breached.";
}
```

---

## Pure PHP

```php
$result = breach_check('password123');

if ($result->isBreached()) {
    echo "Password has been breached.";
}
```

---

# Result Object

`check()` returns an immutable result object.

Example:

```php
$result = BreachPHP::check('password123');
```

Available methods:

```php
$result->isBreached();

$result->count();

$result->hash();

$result->prefix();

$result->suffix();
```

Example:

```php
if ($result->isBreached()) {
    echo "Found {$result->count()} times.";
}
```

---

# Checking Multiple Passwords

```php
$passwords = [
    'password123',
    'correct horse battery staple',
    'mySecretPassword!',
];

foreach ($passwords as $password) {
    $result = BreachPHP::check($password);

    echo sprintf(
        "%s => %s\n",
        $password,
        $result->isBreached() ? 'Breached' : 'Safe'
    );
}
```

---

# Using Dependency Injection

Dependency injection is recommended for larger applications.

```php
use ShamimStack\BreachPHP\Contracts\PasswordCheckerInterface;

final class RegistrationService
{
    public function __construct(
        private readonly PasswordCheckerInterface $checker,
    ) {
    }

    public function validate(string $password): bool
    {
        return ! $this->checker
            ->check($password)
            ->isBreached();
    }
}
```

---

# Using the Service Container

Laravel:

```php
$checker = app(
    \ShamimStack\BreachPHP\Contracts\PasswordCheckerInterface::class
);

$result = $checker->check('password123');
```

---

# Working with Breach Counts

Determine how frequently a password appears in known breaches.

```php
$result = BreachPHP::check('password123');

$count = $result->count();

echo $count;
```

Example:

```php
if ($result->count() > 1000) {
    // Extremely common password
}
```

---

# Hash Information

For debugging or educational purposes, the result object may expose hash information.

```php
$result->hash();

$result->prefix();

$result->suffix();
```

These values are generated locally.

The plaintext password is never transmitted to the breach provider.

---

# Offline Lookup

If local storage is enabled:

```php
$result = BreachPHP::check($password);
```

Lookup order:

1. Local storage
2. Cache
3. Remote provider
4. Store synchronized prefix
5. Return immutable result

This minimizes external API requests over time.

---

# Exception Handling

Catch specific exceptions.

```php
use ShamimStack\BreachPHP\Exceptions\ApiException;

try {

    $result = BreachPHP::check($password);

} catch (ApiException $exception) {

    // Provider unavailable

}
```

Other possible exceptions include:

* ConfigurationException
* StorageException
* ParserException
* TimeoutException
* InvalidPasswordException

Avoid catching generic `Exception` unless absolutely necessary.

---

# Using Helper Functions

BreachPHP provides three helper functions for convenience:

```php
// Check a password and get the full result object
$result = breach_check('password123');

if ($result->isBreached()) {
    echo "Found {$result->count()} times.";
}

// Quick boolean check: is the password safe?
if (breach_is_safe('password123')) {
    echo "Password is safe.";
}

// Quick boolean check: is the password breached?
if (breach_is_breached('password123')) {
    echo "Password has been compromised.";
}
```

Helper functions work in both Laravel and standalone PHP contexts.

In Laravel, they use the container's `PasswordCheckerInterface`.

In standalone PHP, they automatically create a default checker with HIBP provider and array cache.

---

# Working with Local Storage

Enable storage:

```php
'storage' => 'database',
```

Synchronize data:

```bash
php artisan breach:sync
```

View statistics:

```bash
php artisan breach:stats
```

Verify storage integrity:

```bash
php artisan breach:verify
```

The local database grows as additional prefixes are synchronized.

---

# Queueing Synchronization

Enable queues:

```php
'queue' => [

    'enabled' => true,

],
```

Run a worker:

```bash
php artisan queue:work
```

Synchronization jobs can now execute in the background.

---

# Performance Tips

Recommended production configuration:

```php
'storage' => 'database',

'cache' => 'redis',

'queue' => [

    'enabled' => true,

],
```

Benefits:

* Reduced API traffic
* Faster lookups
* Better scalability

---

# Registration Example

```php
$password = $request->string('password');

$result = BreachPHP::check($password);

if ($result->isBreached()) {

    return back()->withErrors([
        'password' => 'Please choose a different password.',
    ]);

}

// Continue registration...
```

---

# Password Reset Example

```php
$result = BreachPHP::check($request->password);

abort_if(
    $result->isBreached(),
    422,
    'The selected password has appeared in known data breaches.'
);
```

---

# Command-Line Usage

Check a password:

```bash
php artisan breach:check
```

Run diagnostics:

```bash
php artisan breach:doctor
```

Display health information:

```bash
php artisan breach:health
```

View statistics:

```bash
php artisan breach:stats
```

---

# Behind the Scenes

When calling:

```php
BreachPHP::check($password);
```

The package performs the following steps:

1. Generates the SHA-1 hash locally.
2. Splits the hash into a prefix and suffix.
3. Looks up the prefix in local storage.
4. Falls back to the configured provider if necessary.
5. Parses the provider response.
6. Stores the synchronized prefix (if enabled).
7. Returns an immutable result object.

At no point is the plaintext password sent over the network.

---

# Best Practices

* Validate passwords during registration and password resets.
* Enable local storage in production.
* Use Redis or another persistent cache.
* Handle package-specific exceptions.
* Prefer dependency injection over facades in reusable services.
* Keep BreachPHP updated to benefit from security and performance improvements.

---

# Next Steps

Continue with the following guides:

* **validation.md** — Integrate BreachPHP with Laravel validation.
* **offline-engine.md** — Learn how the synchronization engine works.
* **commands.md** — Explore every available Artisan command.
* **api.md** — Review the complete public API reference.

BreachPHP is designed to provide secure password breach detection with a clean, intuitive developer experience while remaining extensible for future providers and storage engines.
