<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Storage;

use ShamimStack\BreachPHP\Contracts\StorageInterface;
use ShamimStack\BreachPHP\DTO\PrefixResponse;
use ShamimStack\BreachPHP\DTO\StorageStatistics;

/**
 * Redis storage driver for breach prefix data.
 *
 * Stores SHA-1 prefixes and suffixes in Redis for high-performance lookups.
 */
final class RedisStorage implements StorageInterface
{
    private readonly string $prefixKey;
    private readonly string $dataPrefix;

    public function __construct(
        private readonly \Redis $redis,
        string $keyPrefix = 'breachphp:',
    ) {
        $this->prefixKey = $keyPrefix . 'prefixes';
        $this->dataPrefix = $keyPrefix . 'data:';
    }

    /**
     * {@inheritdoc}
     */
    public function hasPrefix(string $prefix): bool
    {
        return $this->redis->sIsMember($this->prefixKey, strtoupper($prefix));
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $prefix, string $suffix): ?int
    {
        $key = $this->dataPrefix . strtoupper($prefix);
        $count = $this->redis->hGet($key, strtoupper($suffix));

        return $count !== false ? (int) $count : null;
    }

    /**
     * {@inheritdoc}
     */
    public function store(PrefixResponse $response): void
    {
        $prefix = strtoupper($response->prefix());
        $key = $this->dataPrefix . $prefix;

        $this->redis->multi();

        $this->redis->sAdd($this->prefixKey, $prefix);
        $this->redis->del($key);

        $suffixes = [];
        foreach ($response->suffixes() as $record) {
            $suffixes[strtoupper($record['suffix'])] = $record['count'];
        }

        if ($suffixes !== []) {
            $this->redis->hMSet($key, $suffixes);
        }

        $this->redis->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function deletePrefix(string $prefix): void
    {
        $key = $this->dataPrefix . strtoupper($prefix);

        $this->redis->multi();
        $this->redis->sRem($this->prefixKey, strtoupper($prefix));
        $this->redis->del($key);
        $this->redis->exec();
    }

    /**
     * {@inheritdoc}
     */
    public function stats(): StorageStatistics
    {
        $prefixCount = $this->redis->sCard($this->prefixKey);

        $suffixCount = 0;
        $prefixes = $this->redis->sMembers($this->prefixKey);

        if (is_array($prefixes)) {
            foreach ($prefixes as $prefix) {
                $suffixCount += $this->redis->hLen($this->dataPrefix . $prefix);
            }
        }

        return new StorageStatistics(
            prefixes: (int) $prefixCount,
            suffixes: (int) $suffixCount,
            databaseSize: $this->getMemoryUsage(),
            lastSync: null,
        );
    }

    /**
     * Get Redis memory usage.
     */
    private function getMemoryUsage(): ?string
    {
        try {
            $info = $this->redis->info('memory');
            $usedMemory = $info['used_memory'] ?? null;

            if ($usedMemory === null) {
                return null;
            }

            return $this->formatBytes((int) $usedMemory);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Format bytes to human-readable string.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $bytes > 0 ? (int) floor(log($bytes, 1024)) : 0;

        return number_format($bytes / (1024 ** $power), 2) . ' ' . ($units[$power] ?? 'B');
    }
}
