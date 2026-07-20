<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Events;

/**
 * Dispatched before synchronization begins.
 */
final readonly class SyncStarted
{
    public function __construct(
        public string $prefix,
    ) {}
}
