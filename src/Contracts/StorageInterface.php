<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Contracts;

use ShamimStack\BreachPHP\DTO\PrefixResponse;
use ShamimStack\BreachPHP\DTO\StorageStatistics;

/**
 * Defines the contract for local breach data storage.
 *
 * Storage implementations are responsible only for persistence.
 * They must not perform HTTP requests, parsing, or business logic.
 */
interface StorageInterface
{
    /**
     * Determine whether a prefix exists in local storage.
     */
    public function hasPrefix(string $prefix): bool;

    /**
     * Find the breach count for a specific suffix under the given prefix.
     *
     * @return int|null The breach count if found, null otherwise.
     */
    public function find(string $prefix, string $suffix): ?int;

    /**
     * Store a complete prefix response (prefix + all suffixes) in local storage.
     */
    public function store(PrefixResponse $response): void;

    /**
     * Delete a prefix and all its associated suffixes from storage.
     */
    public function deletePrefix(string $prefix): void;

    /**
     * Retrieve storage statistics.
     */
    public function stats(): StorageStatistics;
}
