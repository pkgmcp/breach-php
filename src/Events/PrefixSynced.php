<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Events;

/**
 * Dispatched after a prefix has been successfully synchronized.
 */
final readonly class PrefixSynced
{
    public function __construct(
        public string $prefix,
        public int $suffixCount,
    ) {}
}
