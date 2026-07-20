<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Services;

use ShamimStack\BreachPHP\Contracts\StorageInterface;
use ShamimStack\BreachPHP\DTO\StorageStatistics;

/**
 * Provides storage statistics and reporting.
 */
final class StatisticsService
{
    public function __construct(
        private readonly StorageInterface $storage,
    ) {}

    /**
     * Get comprehensive storage statistics.
     */
    public function getStats(): StorageStatistics
    {
        return $this->storage->stats();
    }
}
