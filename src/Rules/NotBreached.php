<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use ShamimStack\BreachPHP\Contracts\PasswordCheckerInterface;

/**
 * Laravel validation rule that rejects passwords found in known data breaches.
 *
 * Usage:
 * 'password' => ['required', new NotBreached()]
 */
final class NotBreached implements ValidationRule
{
    public function __construct(
        private readonly ?PasswordCheckerInterface $checker = null,
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        $checker = $this->checker ?? $this->resolveChecker();

        $result = $checker->check($value);

        if ($result->isBreached()) {
            $fail('validation.not_breached')->translate([
                'count' => number_format($result->count()),
            ]);
        }
    }

    /**
     * Resolve the password checker from the container.
     */
    private function resolveChecker(): PasswordCheckerInterface
    {
        if (function_exists('app')) {
            return app(PasswordCheckerInterface::class);
        }

        throw new \RuntimeException(
            'The NotBreached rule requires Laravel or a PSR-11 container to be available.'
        );
    }
}
