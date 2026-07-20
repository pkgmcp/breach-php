<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\ValueObjects;

/**
 * Immutable value object representing a 35-character SHA-1 hash suffix.
 */
final readonly class Suffix
{
    private const LENGTH = 35;

    public readonly string $value;

    public function __construct(
        string $value,
    ) {
        $value = strtoupper($value);

        if (strlen($value) !== self::LENGTH) {
            throw new \InvalidArgumentException(
                "A suffix must be exactly " . self::LENGTH . " characters. Got " . strlen($value) . "."
            );
        }

        if (! ctype_alnum($value)) {
            throw new \InvalidArgumentException('A suffix must contain only alphanumeric characters.');
        }

        $this->value = $value;
    }

    /**
     * Create a Suffix from a raw string.
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Create a Suffix from a full SHA-1 hash.
     */
    public static function fromHash(string $hash): self
    {
        return new self(substr($hash, 5));
    }

    /**
     * Get the suffix value.
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Get the string representation.
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
