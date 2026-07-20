<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Events;

/**
 * Dispatched when a lookup is resolved from cache.
 */
final readonly class CacheHit
{
    public function __construct(
        public string $key,
    ) {}
}
