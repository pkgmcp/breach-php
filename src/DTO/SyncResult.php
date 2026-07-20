<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\DTO;

/**
 * Immutable data transfer object containing synchronization results.
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
     * Get the total number of processed prefixes.
     */
    public function processedPrefixes(): int
    {
        return $this->processedPrefixes;
    }

    /**
     * Get the number of successfully stored prefixes.
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
     * Whether the synchronization was successful.
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Get the error message if the synchronization failed.
     */
    public function error(): ?string
    {
        return $this->error;
    }
}
