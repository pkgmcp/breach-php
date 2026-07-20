<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Exceptions;

/**
 * Thrown when the configured provider cannot be contacted or returns an unexpected response.
 */
final class ApiException extends BreachException
{
    public static function providerUnavailable(string $provider, ?\Throwable $previous = null): self
    {
        return new self(
            message: "The provider [{$provider}] is unavailable.",
            previous: $previous,
        );
    }

    public static function invalidResponse(string $provider, ?\Throwable $previous = null): self
    {
        return new self(
            message: "The provider [{$provider}] returned an invalid response.",
            previous: $previous,
        );
    }
}
