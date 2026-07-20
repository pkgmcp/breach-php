<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Storage;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use ShamimStack\BreachPHP\Contracts\StorageInterface;
use ShamimStack\BreachPHP\DTO\PrefixResponse;
use ShamimStack\BreachPHP\DTO\StorageStatistics;
use ShamimStack\BreachPHP\Exceptions\StorageException;

/**
 * SQLite storage driver for breach prefix data.
 *
 * Optimized specifically for SQLite with proper indexing and batch operations.
 */
final class SQLiteStorage implements StorageInterface
{
    private readonly string $prefixesTable;
    private readonly string $suffixesTable;
    private bool $tablesEnsured = false;

    public function __construct(
        private readonly ConnectionInterface $connection,
        string $tablePrefix = 'breachphp_',
    ) {
        $this->prefixesTable = $tablePrefix . 'prefixes';
        $this->suffixesTable = $tablePrefix . 'suffixes';
    }

    /**
     * Ensure tables exist before first query.
     */
    private function ensureTables(): void
    {
        if ($this->tablesEnsured) {
            return;
        }

        if (! $this->connection->getSchemaBuilder()->hasTable($this->prefixesTable)) {
            Schema::connection($this->connection->getName())->create($this->prefixesTable, function (Blueprint $table) {
                $table->id();
                $table->string('prefix', 5)->unique();
                $table->timestamp('synced_at');
                $table->timestamps();
            });
        }

        if (! $this->connection->getSchemaBuilder()->hasTable($this->suffixesTable)) {
            Schema::connection($this->connection->getName())->create($this->suffixesTable, function (Blueprint $table) {
                $table->id();
                $table->foreignId('prefix_id')->constrained($this->prefixesTable)->cascadeOnDelete();
                $table->string('suffix', 35);
                $table->unsignedBigInteger('count');
                $table->timestamps();

                $table->index(['prefix_id', 'suffix']);
            });
        }

        $this->tablesEnsured = true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPrefix(string $prefix): bool
    {
        $this->ensureTables();

        return $this->connection->table($this->prefixesTable)
            ->where('prefix', strtoupper($prefix))
            ->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $prefix, string $suffix): ?int
    {
        $this->ensureTables();

        $prefixId = $this->getPrefixId($prefix);

        if ($prefixId === null) {
            return null;
        }

        $result = $this->connection->table($this->suffixesTable)
            ->where('prefix_id', $prefixId)
            ->where('suffix', strtoupper($suffix))
            ->value('count');

        return $result !== null ? (int) $result : null;
    }

    /**
     * {@inheritdoc}
     */
    public function store(PrefixResponse $response): void
    {
        $this->ensureTables();

        $this->connection->transaction(function () use ($response) {
            $prefix = strtoupper($response->prefix());

            $prefixId = $this->getPrefixId($prefix);

            if ($prefixId === null) {
                $prefixId = $this->connection->table($this->prefixesTable)->insertGetId([
                    'prefix' => $prefix,
                    'synced_at' => now()->toDateTimeString(),
                    'created_at' => now()->toDateTimeString(),
                    'updated_at' => now()->toDateTimeString(),
                ]);
            } else {
                $this->connection->table($this->prefixesTable)
                    ->where('id', $prefixId)
                    ->update([
                        'synced_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                    ]);
            }

            $this->connection->table($this->suffixesTable)
                ->where('prefix_id', $prefixId)
                ->delete();

            $suffixes = array_map(fn (array $record) => [
                'prefix_id' => $prefixId,
                'suffix' => strtoupper($record['suffix']),
                'count' => $record['count'],
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ], $response->suffixes());

            foreach (array_chunk($suffixes, 500) as $chunk) {
                $this->connection->table($this->suffixesTable)->insert($chunk);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function deletePrefix(string $prefix): void
    {
        $this->ensureTables();

        $this->connection->transaction(function () use ($prefix) {
            $prefixId = $this->getPrefixId($prefix);

            if ($prefixId !== null) {
                $this->connection->table($this->suffixesTable)
                    ->where('prefix_id', $prefixId)
                    ->delete();

                $this->connection->table($this->prefixesTable)
                    ->where('id', $prefixId)
                    ->delete();
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function stats(): StorageStatistics
    {
        $this->ensureTables();

        $prefixCount = (int) $this->connection->table($this->prefixesTable)->count();
        $suffixCount = (int) $this->connection->table($this->suffixesTable)->count();
        $lastSync = $this->connection->table($this->prefixesTable)->max('synced_at');

        return new StorageStatistics(
            prefixes: $prefixCount,
            suffixes: $suffixCount,
            databaseSize: $this->getDatabaseSize(),
            lastSync: $lastSync !== null ? (string) $lastSync : null,
        );
    }

    /**
     * Get the prefix ID for a given prefix.
     */
    private function getPrefixId(string $prefix): ?int
    {
        $result = $this->connection->table($this->prefixesTable)
            ->where('prefix', strtoupper($prefix))
            ->value('id');

        return $result !== null ? (int) $result : null;
    }

    /**
     * Get the database file size.
     */
    private function getDatabaseSize(): ?string
    {
        try {
            $databasePath = $this->connection->getDatabasePath();

            if (! file_exists($databasePath)) {
                return null;
            }

            $size = filesize($databasePath);

            if ($size === false) {
                return null;
            }

            return $this->formatBytes($size);
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
