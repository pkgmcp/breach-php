<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\DTO;

/**
 * Immutable data transfer object containing storage statistics.
 */
final readonly class StorageStatistics
{
    public function __construct(
        private int $prefixes = 0,
        private int $suffixes = 0,
        private ?string $databaseSize = null,
        private ?string $lastSync = null,
    ) {}

    /**
     * The total number of stored prefixes.
     */
    public function prefixes(): int
    {
        return $this->prefixes;
    }

    /**
     * The total number of stored suffixes.
     */
    public function suffixes(): int
    {
        return $this->suffixes;
    }

    /**
     * The database size (human-readable).
     */
    public function databaseSize(): ?string
    {
        return $this->databaseSize;
    }

    /**
     * The last synchronization timestamp.
     */
    public function lastSync(): ?string
    {
        return $this->lastSync;
    }
}
