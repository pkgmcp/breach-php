<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Cache;

use Psr\SimpleCache\CacheInterface as Psr16CacheInterface;
use ShamimStack\BreachPHP\Contracts\CacheInterface;

/**
 * Adapter that wraps a PSR-16 cache implementation.
 */
final class Psr16Cache implements CacheInterface
{
    public function __construct(
        private readonly Psr16CacheInterface $cache,
    ) {}

    /**
     * Retrieve a value from the cache by key.
     */
    public function get(string $key): mixed
    {
        return $this->cache->get($key);
    }

    /**
     * Store a value in the cache.
     */
    public function set(string $key, mixed $value, ?int $ttl = null): void
    {
        $this->cache->set($key, $value, $ttl);
    }

    /**
     * Determine whether a key exists in the cache.
     */
    public function has(string $key): bool
    {
        return $this->cache->has($key);
    }

    /**
     * Remove an item from the cache.
     */
    public function forget(string $key): bool
    {
        return $this->cache->delete($key);
    }

    /**
     * Remove all items from the cache.
     */
    public function flush(): void
    {
        $this->cache->clear();
    }
}
