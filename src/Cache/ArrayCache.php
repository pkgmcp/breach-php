<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Cache;

use ShamimStack\BreachPHP\Contracts\CacheInterface;

/**
 * In-memory array cache implementation.
 *
 * Suitable for development and testing.
 * Data is not persisted between requests.
 */
final class ArrayCache implements CacheInterface
{
    /** @var array<string, array{value: mixed, expiry: int|null}> */
    private array $store = [];

    /**
     * Retrieve a value from the cache by key.
     */
    public function get(string $key): mixed
    {
        if (! isset($this->store[$key])) {
            return null;
        }

        $item = $this->store[$key];

        if ($item['expiry'] !== null && $item['expiry'] < time()) {
            unset($this->store[$key]);

            return null;
        }

        return $item['value'];
    }

    /**
     * Store a value in the cache.
     */
    public function set(string $key, mixed $value, ?int $ttl = null): void
    {
        $this->store[$key] = [
            'value' => $value,
            'expiry' => $ttl !== null ? time() + $ttl : null,
        ];
    }

    /**
     * Determine whether a key exists in the cache.
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Remove an item from the cache.
     */
    public function forget(string $key): bool
    {
        if (! isset($this->store[$key])) {
            return false;
        }

        unset($this->store[$key]);

        return true;
    }

    /**
     * Remove all items from the cache.
     */
    public function flush(): void
    {
        $this->store = [];
    }
}
