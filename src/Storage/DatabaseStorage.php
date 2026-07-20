<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Storage;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Builder;
use ShamimStack\BreachPHP\Contracts\StorageInterface;
use ShamimStack\BreachPHP\DTO\PrefixResponse;
use ShamimStack\BreachPHP\DTO\StorageStatistics;
use ShamimStack\BreachPHP\Models\Prefix;
use ShamimStack\BreachPHP\Models\Suffix;

/**
 * Database storage driver for breach prefix data.
 *
 * Stores SHA-1 prefixes and suffixes in a relational database.
 * Supports MySQL, MariaDB, PostgreSQL, and SQLite.
 */
final class DatabaseStorage implements StorageInterface
{
    private readonly string $prefixesTable;
    private readonly string $suffixesTable;

    public function __construct(
        private readonly ConnectionInterface $connection,
        string $tablePrefix = 'breachphp_',
    ) {
        $this->prefixesTable = $tablePrefix . 'prefixes';
        $this->suffixesTable = $tablePrefix . 'suffixes';
    }

    /**
     * Determine whether a prefix exists in local storage.
     */
    public function hasPrefix(string $prefix): bool
    {
        return $this->prefixQuery()
            ->where('prefix', strtoupper($prefix))
            ->exists();
    }

    /**
     * Find the breach count for a specific suffix under the given prefix.
     */
    public function find(string $prefix, string $suffix): ?int
    {
        $prefixId = $this->getPrefixId($prefix);

        if ($prefixId === null) {
            return null;
        }

        $result = $this->suffixQuery()
            ->where('prefix_id', $prefixId)
            ->where('suffix', strtoupper($suffix))
            ->value('count');

        return $result !== null ? (int) $result : null;
    }

    /**
     * Store a complete prefix response in local storage.
     */
    public function store(PrefixResponse $response): void
    {
        $this->connection->transaction(function () use ($response) {
            $prefix = strtoupper($response->prefix());

            // Find or create the prefix
            $prefixModel = $this->prefixQuery()
                ->where('prefix', $prefix)
                ->first();

            if ($prefixModel === null) {
                $prefixModel = $this->prefixQuery()->create([
                    'prefix' => $prefix,
                    'synced_at' => now(),
                ]);
            } else {
                $prefixModel->update(['synced_at' => now()]);
            }

            // Delete existing suffixes for this prefix
            $this->suffixQuery()
                ->where('prefix_id', $prefixModel->id)
                ->delete();

            // Insert new suffixes in batches
            $suffixes = array_map(fn (array $record) => [
                'prefix_id' => $prefixModel->id,
                'suffix' => strtoupper($record['suffix']),
                'count' => $record['count'],
                'created_at' => now(),
                'updated_at' => now(),
            ], $response->suffixes());

            foreach (array_chunk($suffixes, 1000) as $chunk) {
                $this->suffixQuery()->insert($chunk);
            }
        });
    }

    /**
     * Delete a prefix and all its associated suffixes from storage.
     */
    public function deletePrefix(string $prefix): void
    {
        $this->connection->transaction(function () use ($prefix) {
            $prefixModel = $this->prefixQuery()
                ->where('prefix', strtoupper($prefix))
                ->first();

            if ($prefixModel !== null) {
                $this->suffixQuery()
                    ->where('prefix_id', $prefixModel->id)
                    ->delete();

                $prefixModel->delete();
            }
        });
    }

    /**
     * Retrieve storage statistics.
     */
    public function stats(): StorageStatistics
    {
        $prefixCount = (int) $this->prefixQuery()->count();
        $suffixCount = (int) $this->suffixQuery()->count();

        $lastSync = $this->prefixQuery()->max('synced_at');

        return new StorageStatistics(
            prefixes: $prefixCount,
            suffixes: $suffixCount,
            databaseSize: $this->getDatabaseSize(),
            lastSync: $lastSync !== null ? (string) $lastSync : null,
        );
    }

    /**
     * Get a configured query builder for the prefixes table.
     */
    private function prefixQuery(): Builder
    {
        return Prefix::on($this->connection)->setTable($this->prefixesTable);
    }

    /**
     * Get a configured query builder for the suffixes table.
     */
    private function suffixQuery(): Builder
    {
        return Suffix::on($this->connection)->setTable($this->suffixesTable);
    }

    /**
     * Get the prefix ID for a given prefix.
     */
    private function getPrefixId(string $prefix): ?int
    {
        $result = $this->prefixQuery()
            ->where('prefix', strtoupper($prefix))
            ->value('id');

        return $result !== null ? (int) $result : null;
    }

    /**
     * Get the database size (approximate).
     */
    private function getDatabaseSize(): ?string
    {
        try {
            $driver = $this->connection->getDriverName();

            return match ($driver) {
                'sqlite' => $this->getSqliteSize(),
                default => null,
            };
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Get SQLite database file size.
     */
    private function getSqliteSize(): ?string
    {
        $databasePath = $this->connection->getDatabasePath();

        if (! file_exists($databasePath)) {
            return null;
        }

        $size = filesize($databasePath);

        if ($size === false) {
            return null;
        }

        return $this->formatBytes($size);
    }

    /**
     * Format bytes to human-readable string.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;

        return number_format($bytes / (1024 ** $power), 2) . ' ' . ($units[$power] ?? 'B');
    }
}
