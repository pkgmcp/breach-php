<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Concerns;

use ShamimStack\BreachPHP\Contracts\StorageInterface;
use ShamimStack\BreachPHP\DTO\PrefixResponse;
use ShamimStack\BreachPHP\DTO\StorageStatistics;

/**
 * Provides storage interaction capabilities.
 */
trait InteractsWithStorage
{
    private ?StorageInterface $storage = null;

    /**
     * Get the storage instance.
     */
    public function getStorage(): ?StorageInterface
    {
        return $this->storage;
    }

    /**
     * Set the storage instance.
     */
    public function setStorage(StorageInterface $storage): void
    {
        $this->storage = $storage;
    }

    /**
     * Check if storage is available.
     */
    public function hasStorage(): bool
    {
        return $this->storage !== null;
    }

    /**
     * Check if a prefix exists in storage.
     */
    protected function storageHasPrefix(string $prefix): bool
    {
        if ($this->storage === null) {
            return false;
        }

        return $this->storage->hasPrefix($prefix);
    }

    /**
     * Find a suffix count from storage.
     */
    protected function storageFind(string $prefix, string $suffix): ?int
    {
        if ($this->storage === null) {
            return null;
        }

        return $this->storage->find($prefix, $suffix);
    }

    /**
     * Store a prefix response.
     */
    protected function storageStore(PrefixResponse $response): void
    {
        if ($this->storage === null) {
            return;
        }

        $this->storage->store($response);
    }

    /**
     * Get storage statistics.
     */
    protected function storageStats(): ?StorageStatistics
    {
        if ($this->storage === null) {
            return null;
        }

        return $this->storage->stats();
    }
}
