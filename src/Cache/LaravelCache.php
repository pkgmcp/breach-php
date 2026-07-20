<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Cache;

use Illuminate\Contracts\Cache\Repository;
use ShamimStack\BreachPHP\Contracts\CacheInterface;

/**
 * Adapter that wraps a Laravel cache repository.
 */
final class LaravelCache implements CacheInterface
{
    public function __construct(
        private readonly Repository $cache,
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
        $this->cache->put($key, $value, $ttl);
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
        return $this->cache->forget($key);
    }

    /**
     * Remove all items from the cache.
     */
    public function flush(): void
    {
        $this->cache->flush();
    }
}
