<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Exceptions;

/**
 * Thrown when package configuration is invalid or incomplete.
 */
final class ConfigurationException extends BreachException
{
    public static function missing(string $key): self
    {
        return new self(
            message: "The configuration key [{$key}] is missing.",
        );
    }

    public static function invalid(string $key, string $reason): self
    {
        return new self(
            message: "The configuration key [{$key}] is invalid: {$reason}.",
        );
    }

    public static function driverNotSupported(string $type, string $driver): self
    {
        return new self(
            message: "The {$type} driver [{$driver}] is not supported.",
        );
    }

    public static function invalidValue(string $key, string|int|float|null $value, string $reason): self
    {
        return new self(
            message: "The configuration value for [{$key}] ({$value}) is invalid: {$reason}.",
        );
    }
}
