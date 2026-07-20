<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationAttribute;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Validation\Validator;
use ShamimStack\BreachPHP\Contracts\PasswordCheckerInterface;

/**
 * Validation rule that fails if a password HAS been breached.
 *
 * Use this rule when you want to explicitly check that a password
 * is in a breach database (opposite of NotBreached).
 */
final class Breached implements ValidationAttribute, ValidatorAwareRule
{
    private ?Validator $validator = null;

    public function __construct(
        private readonly PasswordCheckerInterface $checker,
        private ?string $message = null,
    ) {}

    /**
     * Set the current validator.
     */
    public function setValidator(Validator $validator): static
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $result = $this->checker->check($value);

        if (! $result->isBreached()) {
            $message = $this->message ?? 'The :attribute has not been found in known data breaches.';

            $fail($message);
        }
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return $this->message ?? 'The :attribute has not been found in known data breaches.';
    }
}
