<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Events;

/**
 * Dispatched when synchronization fails.
 */
final readonly class SyncFailed
{
    public function __construct(
        public string $prefix,
        public \Throwable $exception,
    ) {}
}
