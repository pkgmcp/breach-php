<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\DTO;

/**
 * Immutable data transfer object returned by every password check.
 *
 * Contains the breach status, count, hash information, and source metadata.
 */
final readonly class PasswordResult
{
    public function __construct(
        private bool $isBreached,
        private int $count,
        private string $hash,
        private string $prefix,
        private string $suffix,
        private string $provider,
        private string $source,
    ) {}

    /**
     * Create a safe (non-breached) result.
     */
    public static function safe(string $hash, string $prefix, string $suffix, string $provider, string $source): self
    {
        return new self(
            isBreached: false,
            count: 0,
            hash: $hash,
            prefix: $prefix,
            suffix: $suffix,
            provider: $provider,
            source: $source,
        );
    }

    /**
     * Create a breached result.
     */
    public static function breached(int $count, string $hash, string $prefix, string $suffix, string $provider, string $source): self
    {
        return new self(
            isBreached: true,
            count: $count,
            hash: $hash,
            prefix: $prefix,
            suffix: $suffix,
            provider: $provider,
            source: $source,
        );
    }

    /**
     * Whether the password has been found in the breach database.
     */
    public function isBreached(): bool
    {
        return $this->isBreached;
    }

    /**
     * Whether the password is safe (not found in any breach).
     */
    public function isSafe(): bool
    {
        return ! $this->isBreached;
    }

    /**
     * The number of times the password appears in known breaches.
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * The SHA-1 hash generated locally.
     */
    public function hash(): string
    {
        return $this->hash;
    }

    /**
     * The first five characters of the SHA-1 hash (prefix).
     */
    public function prefix(): string
    {
        return $this->prefix;
    }

    /**
     * The remaining 35 characters of the SHA-1 hash (suffix).
     */
    public function suffix(): string
    {
        return $this->suffix;
    }

    /**
     * The provider that handled the request.
     */
    public function provider(): string
    {
        return $this->provider;
    }

    /**
     * The lookup source (cache, storage, or provider).
     */
    public function source(): string
    {
        return $this->source;
    }
}
