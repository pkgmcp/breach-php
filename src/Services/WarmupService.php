<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Services;

use ShamimStack\BreachPHP\Contracts\ProviderInterface;
use ShamimStack\BreachPHP\Contracts\StorageInterface;
use ShamimStack\BreachPHP\DTO\PrefixResponse;
use ShamimStack\BreachPHP\DTO\SyncResult;

/**
 * Handles warmup synchronization of frequently accessed prefixes.
 */
final class WarmupService
{
    private const COMMON_PREFIXES = [
        '00000', '00001', '00002', '00003', '00004',
        '11111', '22222', '33333', '44444', '55555',
        '66666', '77777', '88888', '99999', 'AAAAA',
        'BBBBB', 'CCCCC', 'DDDDD', 'EEEEE', 'FFFFF',
    ];

    public function __construct(
        private readonly ProviderInterface $provider,
        private readonly StorageInterface $storage,
        private readonly int $batchSize = 10,
    ) {}

    /**
     * Warmup storage with common prefixes.
     */
    public function warmup(?int $limit = null): SyncResult
    {
        $prefixes = $limit !== null
            ? array_slice(self::COMMON_PREFIXES, 0, $limit)
            : self::COMMON_PREFIXES;

        $processed = 0;
        $storedPrefixes = 0;
        $storedSuffixes = 0;
        $lastError = null;

        foreach ($prefixes as $prefix) {
            if ($this->storage->hasPrefix($prefix)) {
                continue;
            }

            try {
                $response = $this->provider->fetch($prefix);
                $prefixResponse = PrefixResponse::fromProviderResponse($response);
                $this->storage->store($prefixResponse);

                $storedPrefixes++;
                $storedSuffixes += $prefixResponse->suffixCount();
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
            }

            $processed++;
        }

        return new SyncResult(
            processedPrefixes: $processed,
            storedPrefixes: $storedPrefixes,
            storedSuffixes: $storedSuffixes,
            success: $lastError === null,
            error: $lastError,
        );
    }

    /**
     * Get the list of common prefixes used for warmup.
     *
     * @return string[]
     */
    public function getCommonPrefixes(): array
    {
        return self::COMMON_PREFIXES;
    }

    /**
     * Check how many common prefixes are already synchronized.
     */
    public function getSyncedCount(): int
    {
        $count = 0;

        foreach (self::COMMON_PREFIXES as $prefix) {
            if ($this->storage->hasPrefix($prefix)) {
                $count++;
            }
        }

        return $count;
    }
}
