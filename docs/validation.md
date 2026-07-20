# Validation

BreachPHP integrates with Laravel's validation system, allowing you to prevent users from choosing passwords that have appeared in known data breaches.

This guide demonstrates the available validation rules and recommended usage patterns.

---

# Why Validate Breached Passwords?

A password that appears in a public breach is more likely to be targeted in credential stuffing and password reuse attacks.

Rejecting known compromised passwords improves account security without requiring users to understand the underlying breach data.

BreachPHP performs password checks using the **k-Anonymity** model, meaning the plaintext password is never transmitted to the remote provider.

---

# Basic Validation Rule

Use the built-in validation rule.

```php id="fjf7d2"
use ShamimStack\BreachPHP\Rules\NotBreached;

$request->validate([
    'password' => [
        'required',
        'string',
        'min:12',
        new NotBreached(),
    ],
]);
```

If the password exists in the breach database, validation fails.

---

# Using Validator

```php id="vy8mh6"
use Illuminate\Support\Facades\Validator;
use ShamimStack\BreachPHP\Rules\NotBreached;

Validator::make($request->all(), [
    'password' => [
        'required',
        'confirmed',
        new NotBreached(),
    ],
])->validate();
```

---

# Form Request Example

```php id="m7cryl"
use Illuminate\Foundation\Http\FormRequest;
use ShamimStack\BreachPHP\Rules\NotBreached;

final class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:12',
                new NotBreached(),
            ],
        ];
    }
}
```

Using a dedicated `FormRequest` keeps validation logic organized and reusable.

---

# Combining with Laravel Password Rules

BreachPHP works alongside Laravel's password validation rules.

```php id="uh3pzy"
use Illuminate\Validation\Rules\Password;
use ShamimStack\BreachPHP\Rules\NotBreached;

$request->validate([
    'password' => [
        'required',
        Password::min(12)
            ->mixedCase()
            ->numbers()
            ->symbols(),

        new NotBreached(),
    ],
]);
```

This combines password complexity requirements with breach detection.

---

# Breached Rule

The `Breached` rule is the opposite of `NotBreached`. It **fails** if the password is NOT found in the breach database.

Use this when you want to explicitly verify that a password has been compromised:

```php
use ShamimStack\BreachPHP\Rules\Breached;

$request->validate([
    'password' => [
        'required',
        'string',
        new Breached(),
    ],
]);
```

Custom error message:

```php
new Breached(message: 'The :attribute must be a known compromised password.')
```

This is useful for administrative tools or security audit workflows where you need to confirm that a password exists in breach databases.

---

# Custom Error Message

Customize the validation message by overriding the rule's message method or by using Laravel's language files.

Example language entry:

```php id="gt4m7e"
'password' => [
    'not_breached' => 'This password has appeared in known data breaches. Please choose a different password.',
],
```

Then return the translation from the validation rule.

---

# Ignoring Provider Failures

Some applications may prefer to allow validation to continue if the external provider is temporarily unavailable.

Example configuration:

```php id="f7n4gk"
'fail_open' => false,
```

Recommended behavior:

| Setting | Behavior                                                  |
| ------- | --------------------------------------------------------- |
| `true`  | Validation succeeds if the provider cannot be reached.    |
| `false` | Validation fails if the breach check cannot be completed. |

Choose the option that matches your application's security requirements.

---

# Offline Validation

When local storage is enabled:

```php id="94ov2d"
'storage' => 'database',
```

Validation first checks the local database.

Lookup order:

1. Local storage
2. Cache
3. Remote provider
4. Store synchronized prefix
5. Return validation result

If the required prefix has already been synchronized, validation completes without contacting the remote provider.

---

# Dependency Injection

The validation rule should resolve the password checker through dependency injection rather than creating it directly.

Example:

```php id="u3pbx2"
public function __construct(
    private readonly PasswordCheckerInterface $checker,
) {
}
```

This keeps the rule testable and independent of specific implementations.

---

# Exception Handling

Validation rules should handle package exceptions gracefully.

Examples include:

* ApiException
* TimeoutException
* StorageException
* ConfigurationException

Unexpected failures should result in predictable validation behavior based on the configured `fail_open` policy.

---

# Testing Validation

Example feature test:

```php id="4q9uhf"
it('rejects breached passwords', function () {
    $response = $this->post('/register', [
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('password');
});
```

Mock the breach provider in tests to ensure deterministic results.

---

# Best Practices

* Combine breach validation with Laravel's built-in password rules.
* Require password confirmation for registration and password reset forms.
* Use a minimum password length of at least 12 characters.
* Enable local storage in production to reduce external API requests.
* Decide whether your application should fail open or fail closed when the provider is unavailable.
* Write feature tests covering both breached and non-breached passwords.

---

# Common Use Cases

## User Registration

Reject compromised passwords before creating a new account.

---

## Password Reset

Ensure newly chosen passwords have not appeared in known breaches.

---

## Password Change

Validate updated passwords whenever a user changes their credentials.

---

## Administrative User Creation

Protect administrator accounts by enforcing the same validation rules.

---

# Security Notes

* Plaintext passwords are never transmitted.
* SHA-1 hashes are generated locally.
* Only the five-character hash prefix is sent to the provider.
* Validation is compatible with the k-Anonymity protocol used by Have I Been Pwned.
* Local storage can eliminate most provider requests once prefixes have been synchronized.

---

# Next Steps

Continue with:

* **offline-engine.md** — Learn how local synchronization works.
* **commands.md** — Explore maintenance and synchronization commands.
* **api.md** — Review the complete public API.

Adding breach validation to authentication workflows provides an additional layer of protection against password reuse and helps users choose safer credentials without changing their experience.
