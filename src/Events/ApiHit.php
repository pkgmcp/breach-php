<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Events;

/**
 * Dispatched when the configured provider is queried.
 */
final readonly class ApiHit
{
    public function __construct(
        public string $prefix,
        public string $provider,
    ) {}
}
