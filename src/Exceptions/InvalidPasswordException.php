<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Exceptions;

/**
 * Thrown when the supplied password is invalid for processing.
 */
final class InvalidPasswordException extends BreachException
{
    public static function emptyPassword(): self
    {
        return new self(
            message: 'The password cannot be empty.',
        );
    }

    public static function tooLong(int $maxLength): self
    {
        return new self(
            message: "The password exceeds the maximum length of {$maxLength} characters.",
        );
    }
}
