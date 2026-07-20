<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Exceptions;

/**
 * Thrown when the configured storage driver encounters an error.
 */
final class StorageException extends BreachException
{
    public static function operationFailed(string $operation, ?\Throwable $previous = null): self
    {
        return new self(
            message: "Storage operation [{$operation}] failed.",
            previous: $previous,
        );
    }

    public static function driverNotFound(string $driver): self
    {
        return new self(
            message: "Storage driver [{$driver}] is not supported.",
        );
    }
}
