<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Services;

use ShamimStack\BreachPHP\Contracts\ProviderInterface;
use ShamimStack\BreachPHP\Contracts\StorageInterface;
use ShamimStack\BreachPHP\DTO\PrefixResponse;
use ShamimStack\BreachPHP\DTO\SyncResult;
use ShamimStack\BreachPHP\Events\PrefixSynced;
use ShamimStack\BreachPHP\Events\SyncCompleted;
use ShamimStack\BreachPHP\Events\SyncFailed;
use ShamimStack\BreachPHP\Events\SyncStarted;

/**
 * Handles synchronization of breach prefix data from the provider to local storage.
 */
final class SyncService
{
    public function __construct(
        private readonly ProviderInterface $provider,
        private readonly StorageInterface $storage,
    ) {}

    /**
     * Synchronize a specific prefix from the provider.
     */
    public function syncPrefix(string $prefix): SyncResult
    {
        $prefix = strtoupper($prefix);

        $this->dispatchEvent(new SyncStarted(prefix: $prefix));

        try {
            $response = $this->provider->fetch($prefix);

            $prefixResponse = PrefixResponse::fromProviderResponse($response);

            $this->storage->store($prefixResponse);

            $this->dispatchEvent(new PrefixSynced(
                prefix: $prefix,
                suffixCount: $prefixResponse->suffixCount(),
            ));

            $this->dispatchEvent(new SyncCompleted(
                prefix: $prefix,
                duration: 0,
            ));

            return new SyncResult(
                processedPrefixes: 1,
                storedPrefixes: 1,
                storedSuffixes: $prefixResponse->suffixCount(),
                success: true,
            );
        } catch (\Throwable $e) {
            $this->dispatchEvent(new SyncFailed(
                prefix: $prefix,
                exception: $e,
            ));

            return new SyncResult(
                processedPrefixes: 1,
                storedPrefixes: 0,
                storedSuffixes: 0,
                success: false,
                error: $e->getMessage(),
            );
        }
    }

    /**
     * Synchronize multiple prefixes.
     *
     * @param  string[]  $prefixes  The list of prefixes to synchronize.
     */
    public function syncPrefixes(array $prefixes): SyncResult
    {
        $processed = 0;
        $storedPrefixes = 0;
        $storedSuffixes = 0;
        $lastError = null;

        foreach ($prefixes as $prefix) {
            $result = $this->syncPrefix($prefix);

            $processed += $result->processedPrefixes();
            $storedPrefixes += $result->storedPrefixes();
            $storedSuffixes += $result->storedSuffixes();

            if (! $result->isSuccess()) {
                $lastError = $result->error();
            }
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
     * Check if a prefix is already synchronized.
     */
    public function isSynchronized(string $prefix): bool
    {
        return $this->storage->hasPrefix(strtoupper($prefix));
    }

    /**
     * Dispatch an event if the dispatcher is available.
     */
    private function dispatchEvent(object $event): void
    {
        if (function_exists('event')) {
            try {
                event($event);
            } catch (\Throwable) {
                // Silently ignore event dispatch failures
            }
        }
    }
}
