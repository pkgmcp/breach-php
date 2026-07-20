<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Cache;

use ShamimStack\BreachPHP\Contracts\CacheInterface;

/**
 * Null cache implementation that performs no caching.
 *
 * Useful when caching is disabled via configuration ('cache' => 'none').
 * All get() calls return null, all set() calls are no-ops.
 */
final class NullCache implements CacheInterface
{
    /**
     * Retrieve a value from the cache by key.
     *
     * Always returns null since caching is disabled.
     */
    public function get(string $key): mixed
    {
        return null;
    }

    /**
     * Store a value in the cache.
     *
     * This is a no-op since caching is disabled.
     */
    public function set(string $key, mixed $value, ?int $ttl = null): void
    {
        // No-op: caching is disabled
    }

    /**
     * Determine whether a key exists in the cache.
     *
     * Always returns false since caching is disabled.
     */
    public function has(string $key): bool
    {
        return false;
    }

    /**
     * Remove an item from the cache.
     *
     * Always returns false since nothing is cached.
     */
    public function forget(string $key): bool
    {
        return false;
    }

    /**
     * Remove all items from the cache.
     *
     * This is a no-op since nothing is cached.
     */
    public function flush(): void
    {
        // No-op: nothing to flush
    }
}
