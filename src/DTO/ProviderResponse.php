<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\DTO;

/**
 * Immutable data transfer object representing a provider's response to a prefix query.
 *
 * Contains the prefix and all associated suffix records with their breach counts.
 */
final readonly class ProviderResponse
{
    /**
     * @param  string  $prefix  The five-character SHA-1 prefix.
     * @param  array<int, array{suffix: string, count: int}>  $suffixes  The suffix records.
     */
    public function __construct(
        private string $prefix,
        private array $suffixes,
    ) {}

    /**
     * Get the prefix.
     */
    public function prefix(): string
    {
        return $this->prefix;
    }

    /**
     * Get all suffix records.
     *
     * @return array<int, array{suffix: string, count: int}>
     */
    public function suffixes(): array
    {
        return $this->suffixes;
    }

    /**
     * Find the count for a specific suffix.
     *
     * @return int|null The breach count if found, null otherwise.
     */
    public function findCount(string $suffix): ?int
    {
        foreach ($this->suffixes as $record) {
            if (strtoupper($record['suffix']) === strtoupper($suffix)) {
                return $record['count'];
            }
        }

        return null;
    }

    /**
     * Get the total number of suffix records.
     */
    public function suffixCount(): int
    {
        return count($this->suffixes);
    }
}
