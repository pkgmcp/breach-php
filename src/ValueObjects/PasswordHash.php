<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\ValueObjects;

use ShamimStack\BreachPHP\Exceptions\InvalidPasswordException;

/**
 * Immutable value object representing a SHA-1 hash of a password.
 *
 * The hash is always stored in uppercase hexadecimal format.
 */
final readonly class PasswordHash
{
    private const HASH_LENGTH = 40;

    public function __construct(
        private string $value,
    ) {
        if ($value === '') {
            throw InvalidPasswordException::emptyPassword();
        }

        if (strlen($value) !== self::HASH_LENGTH) {
            throw new \InvalidArgumentException(
                "A SHA-1 hash must be exactly " . self::HASH_LENGTH . " characters. Got " . strlen($value) . "."
            );
        }
    }

    /**
     * Create a PasswordHash from a plaintext password.
     */
    public static function fromPassword(string $password): self
    {
        if ($password === '') {
            throw InvalidPasswordException::emptyPassword();
        }

        return new self(strtoupper(sha1($password)));
    }

    /**
     * Get the full hash value.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Get the first five characters (prefix).
     */
    public function prefix(): string
    {
        return substr($this->value, 0, 5);
    }

    /**
     * Get the remaining 35 characters (suffix).
     */
    public function suffix(): string
    {
        return substr($this->value, 5);
    }

    /**
     * Get the string representation.
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
