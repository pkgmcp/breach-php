<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Tests\Helpers;

use ShamimStack\BreachPHP\Contracts\CacheInterface;

/**
 * In-memory cache implementation for testing.
 */
final class InMemoryCache implements CacheInterface
{
    private array $store = [];

    public function get(string $key, mixed $default = null): mixed
    {
        if (! isset($this->store[$key])) {
            return $default;
        }

        $item = $this->store[$key];

        if ($item['expires_at'] !== null && $item['expires_at'] < time()) {
            unset($this->store[$key]);

            return $default;
        }

        return $item['value'];
    }

    public function set(string $key, mixed $value, int $ttl = 0): bool
    {
        $this->store[$key] = [
            'value' => $value,
            'expires_at' => $ttl > 0 ? time() + $ttl : null,
        ];

        return true;
    }

    public function has(string $key): bool
    {
        if (! isset($this->store[$key])) {
            return false;
        }

        $item = $this->store[$key];

        if ($item['expires_at'] !== null && $item['expires_at'] < time()) {
            unset($this->store[$key]);

            return false;
        }

        return true;
    }

    public function forget(string $key): bool
    {
        if (isset($this->store[$key])) {
            unset($this->store[$key]);

            return true;
        }

        return false;
    }

    public function clear(): bool
    {
        $this->store = [];

        return true;
    }

    /**
     * Get the number of items in the cache.
     */
    public function count(): int
    {
        return count($this->store);
    }

    /**
     * Check if the cache is empty.
     */
    public function isEmpty(): bool
    {
        return $this->store === [];
    }
}
