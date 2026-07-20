<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Events;

/**
 * Dispatched when a lookup is resolved from local storage.
 */
final readonly class StorageHit
{
    public function __construct(
        public string $prefix,
    ) {}
}
