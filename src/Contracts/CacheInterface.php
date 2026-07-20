<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Contracts;

/**
 * Defines the contract for cache implementations.
 *
 * Cache drivers reduce repeated lookups for the same prefix.
 * They must not contain business logic or perform HTTP requests.
 */
interface CacheInterface
{
    /**
     * Retrieve a value from the cache by key.
     *
     * @return mixed The cached value, or null if not found.
     */
    public function get(string $key): mixed;

    /**
     * Store a value in the cache.
     *
     * @param  mixed  $value  The value to cache.
     * @param  int|null  $ttl  Time-to-live in seconds. Null for default TTL.
     */
    public function set(string $key, mixed $value, ?int $ttl = null): void;

    /**
     * Determine whether a key exists in the cache.
     */
    public function has(string $key): bool;

    /**
     * Remove an item from the cache.
     */
    public function forget(string $key): bool;

    /**
     * Remove all items from the cache.
     */
    public function flush(): void;
}
