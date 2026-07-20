<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Events;

/**
 * Dispatched after synchronization completes successfully.
 */
final readonly class SyncCompleted
{
    public function __construct(
        public string $prefix,
        public int $duration,
    ) {}
}
