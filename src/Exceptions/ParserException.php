<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Exceptions;

/**
 * Thrown when a provider response cannot be parsed.
 */
final class ParserException extends BreachException
{
    public static function invalidFormat(string $expected, ?\Throwable $previous = null): self
    {
        return new self(
            message: "The response does not match the expected format: {$expected}.",
            previous: $previous,
        );
    }

    public static function emptyResponse(?string $prefix = null, ?\Throwable $previous = null): self
    {
        $message = 'The provider returned an empty response.';

        if ($prefix !== null) {
            $message = "The provider returned an empty response for prefix [{$prefix}].";
        }

        return new self(message: $message, previous: $previous);
    }
}
