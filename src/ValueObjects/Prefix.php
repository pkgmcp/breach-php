<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\ValueObjects;

/**
 * Immutable value object representing a five-character SHA-1 hash prefix.
 */
final readonly class Prefix
{
    private const LENGTH = 5;

    public readonly string $value;

    public function __construct(
        string $value,
    ) {
        $value = strtoupper($value);

        if (strlen($value) !== self::LENGTH) {
            throw new \InvalidArgumentException(
                "A prefix must be exactly " . self::LENGTH . " characters. Got " . strlen($value) . "."
            );
        }

        if (! ctype_alnum($value)) {
            throw new \InvalidArgumentException('A prefix must contain only alphanumeric characters.');
        }

        $this->value = $value;
    }

    /**
     * Create a Prefix from a raw string.
     */
    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * Create a Prefix from a full SHA-1 hash.
     */
    public static function fromHash(string $hash): self
    {
        return new self(substr($hash, 0, self::LENGTH));
    }

    /**
     * Get the prefix value.
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
