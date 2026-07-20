<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\DTO;

/**
 * Immutable data transfer object representing a prefix response suitable for storage.
 *
 * This DTO carries the prefix and its associated suffix data for persistence operations.
 */
final readonly class PrefixResponse
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
     * Create from a ProviderResponse.
     */
    public static function fromProviderResponse(ProviderResponse $response): self
    {
        return new self(
            prefix: $response->prefix(),
            suffixes: $response->suffixes(),
        );
    }

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
     * Get the total number of suffix records.
     */
    public function suffixCount(): int
    {
        return count($this->suffixes);
    }
}
