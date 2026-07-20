<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Storage;

use ShamimStack\BreachPHP\Contracts\StorageInterface;
use ShamimStack\BreachPHP\DTO\PrefixResponse;
use ShamimStack\BreachPHP\DTO\StorageStatistics;

/**
 * Null storage implementation that performs no persistence.
 *
 * Useful when storage is disabled via configuration ('storage' => 'none').
 * All find() calls return null, all store() calls are no-ops.
 */
final class NullStorage implements StorageInterface
{
    /**
     * Determine whether a prefix exists in local storage.
     *
     * Always returns false since storage is disabled.
     */
    public function hasPrefix(string $prefix): bool
    {
        return false;
    }

    /**
     * Find the breach count for a specific suffix under the given prefix.
     *
     * Always returns null since storage is disabled.
     */
    public function find(string $prefix, string $suffix): ?int
    {
        return null;
    }

    /**
     * Store a complete prefix response.
     *
     * This is a no-op since storage is disabled.
     */
    public function store(PrefixResponse $response): void
    {
        // No-op: storage is disabled
    }

    /**
     * Delete a prefix and all its associated suffixes.
     *
     * This is a no-op since storage is disabled.
     */
    public function deletePrefix(string $prefix): void
    {
        // No-op: storage is disabled
    }

    /**
     * Retrieve storage statistics.
     *
     * Returns zeroed statistics since storage is disabled.
     */
    public function stats(): StorageStatistics
    {
        return new StorageStatistics(
            totalPrefixes: 0,
            totalSuffixes: 0,
            databaseSize: 0,
        );
    }
}
