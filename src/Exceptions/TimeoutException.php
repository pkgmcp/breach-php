<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Exceptions;

/**
 * Thrown when an HTTP request exceeds the configured timeout.
 */
final class TimeoutException extends BreachException
{
    public static function requestTimeout(int $timeout, ?\Throwable $previous = null): self
    {
        return new self(
            message: "The request timed out after {$timeout} seconds.",
            previous: $previous,
        );
    }
}
