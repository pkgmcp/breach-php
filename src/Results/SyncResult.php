<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Results;

/**
 * Immutable result object for synchronization operations.
 */
final readonly class SyncResult
{
    public function __construct(
        private int $processedPrefixes = 0,
        private int $storedPrefixes = 0,
        private int $storedSuffixes = 0,
        private bool $success = true,
        private ?string $error = null,
    ) {}

    /**
     * Create a successful result.
     */
    public static function success(int $processed = 0, int $stored = 0, int $suffixes = 0): self
    {
        return new self(
            processedPrefixes: $processed,
            storedPrefixes: $stored,
            storedSuffixes: $suffixes,
            success: true,
        );
    }

    /**
     * Create a failed result.
     */
    public static function failed(string $error, int $processed = 0): self
    {
        return new self(
            processedPrefixes: $processed,
            success: false,
            error: $error,
        );
    }

    /**
     * Get the number of processed prefixes.
     */
    public function processedPrefixes(): int
    {
        return $this->processedPrefixes;
    }

    /**
     * Get the number of stored prefixes.
     */
    public function storedPrefixes(): int
    {
        return $this->storedPrefixes;
    }

    /**
     * Get the number of stored suffixes.
     */
    public function storedSuffixes(): int
    {
        return $this->storedSuffixes;
    }

    /**
     * Check if the operation was successful.
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Get the error message if failed.
     */
    public function error(): ?string
    {
        return $this->error;
    }

    /**
     * Convert to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'processed_prefixes' => $this->processedPrefixes,
            'stored_prefixes' => $this->storedPrefixes,
            'stored_suffixes' => $this->storedSuffixes,
            'success' => $this->success,
            'error' => $this->error,
        ];
    }
}
