<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Services;

use ShamimStack\BreachPHP\Contracts\CacheInterface;
use ShamimStack\BreachPHP\Contracts\HashGeneratorInterface;
use ShamimStack\BreachPHP\Contracts\PasswordCheckerInterface;
use ShamimStack\BreachPHP\Contracts\ProviderInterface;
use ShamimStack\BreachPHP\Contracts\StorageInterface;
use ShamimStack\BreachPHP\DTO\PasswordResult;
use ShamimStack\BreachPHP\DTO\PrefixResponse;
use ShamimStack\BreachPHP\Events\ApiHit;
use ShamimStack\BreachPHP\Events\CacheHit;
use ShamimStack\BreachPHP\Events\PasswordBreached;
use ShamimStack\BreachPHP\Events\PasswordChecked;
use ShamimStack\BreachPHP\Events\PasswordSafe;
use ShamimStack\BreachPHP\Events\StorageHit;
use ShamimStack\BreachPHP\Exceptions\InvalidPasswordException;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Core password checking service.
 *
 * Orchestrates the password breach detection workflow:
 * 1. Generate SHA-1 hash locally
 * 2. Check cache
 * 3. Check local storage
 * 4. Query the configured provider
 * 5. Store the response locally
 * 6. Return an immutable result
 */
final class PasswordChecker implements PasswordCheckerInterface
{
    private const SOURCE_CACHE = 'cache';
    private const SOURCE_STORAGE = 'storage';
    private const SOURCE_PROVIDER = 'provider';
    private const PROVIDER_NAME = 'hibp';
    private const CACHE_TTL = 86400; // 24 hours
    private const MAX_PASSWORD_LENGTH = 1024;

    public function __construct(
        private readonly ProviderInterface $provider,
        private readonly HashGeneratorInterface $hasher,
        private readonly ?StorageInterface $storage = null,
        private readonly ?CacheInterface $cache = null,
        private readonly ?EventDispatcherInterface $eventDispatcher = null,
        private readonly bool $storePrefixes = true,
    ) {}

    /**
     * Check whether the given password exists in the configured breach database.
     */
    public function check(string $password): PasswordResult
    {
        // Validate password length to prevent DoS via extremely long inputs
        if (mb_strlen($password, 'UTF-8') > self::MAX_PASSWORD_LENGTH) {
            throw InvalidPasswordException::tooLong(self::MAX_PASSWORD_LENGTH);
        }

        $hash = $this->hasher->hash($password);
        $prefix = substr($hash, 0, 5);
        $suffix = substr($hash, 5);

        // 1. Check cache
        $cachedCount = $this->getCachedCount($prefix, $suffix);

        if ($cachedCount !== null) {
            $this->dispatchEvent(new CacheHit(key: "breachphp:{$prefix}:{$suffix}"));

            return $this->buildResult(
                count: $cachedCount,
                hash: $hash,
                prefix: $prefix,
                suffix: $suffix,
                source: self::SOURCE_CACHE,
            );
        }

        // 2. Check local storage
        if ($this->storage !== null) {
            $storageCount = $this->storage->find($prefix, $suffix);

            if ($storageCount !== null) {
                $this->cacheCount($prefix, $suffix, $storageCount);
                $this->dispatchEvent(new StorageHit(prefix: $prefix));

                return $this->buildResult(
                    count: $storageCount,
                    hash: $hash,
                    prefix: $prefix,
                    suffix: $suffix,
                    source: self::SOURCE_STORAGE,
                );
            }
        }

        // 3. Query the provider
        $providerResponse = $this->provider->fetch($prefix);
        $this->dispatchEvent(new ApiHit(prefix: $prefix, provider: self::PROVIDER_NAME));

        // 4. Find the suffix count
        $count = $providerResponse->findCount($suffix) ?? 0;

        // 5. Store the response locally
        if ($this->storePrefixes && $this->storage !== null && $providerResponse->suffixCount() > 0) {
            $this->storage->store(PrefixResponse::fromProviderResponse($providerResponse));
        }

        // 6. Cache the result
        $this->cacheCount($prefix, $suffix, $count);

        // 7. Build and return the result
        $result = $this->buildResult(
            count: $count,
            hash: $hash,
            prefix: $prefix,
            suffix: $suffix,
            source: self::SOURCE_PROVIDER,
        );

        // 8. Dispatch events
        $this->dispatchEvent(new PasswordChecked(result: $result));

        if ($result->isBreached()) {
            $this->dispatchEvent(new PasswordBreached(result: $result));
        } else {
            $this->dispatchEvent(new PasswordSafe(result: $result));
        }

        return $result;
    }

    /**
     * Build a PasswordResult.
     */
    private function buildResult(int $count, string $hash, string $prefix, string $suffix, string $source): PasswordResult
    {
        return $count > 0
            ? PasswordResult::breached(
                count: $count,
                hash: $hash,
                prefix: $prefix,
                suffix: $suffix,
                provider: self::PROVIDER_NAME,
                source: $source,
            )
            : PasswordResult::safe(
                hash: $hash,
                prefix: $prefix,
                suffix: $suffix,
                provider: self::PROVIDER_NAME,
                source: $source,
            );
    }

    /**
     * Get a cached breach count.
     */
    private function getCachedCount(string $prefix, string $suffix): ?int
    {
        if ($this->cache === null) {
            return null;
        }

        $key = "breachphp:{$prefix}:{$suffix}";
        $cached = $this->cache->get($key);

        if ($cached === null || ! is_int($cached)) {
            return null;
        }

        return $cached;
    }

    /**
     * Cache a breach count.
     */
    private function cacheCount(string $prefix, string $suffix, int $count): void
    {
        if ($this->cache === null) {
            return;
        }

        $key = "breachphp:{$prefix}:{$suffix}";
        $this->cache->set($key, $count, self::CACHE_TTL);
    }

    /**
     * Dispatch an event if the dispatcher is available.
     */
    private function dispatchEvent(object $event): void
    {
        if ($this->eventDispatcher === null) {
            return;
        }

        try {
            $this->eventDispatcher->dispatch($event);
        } catch (\Throwable) {
            // Silently ignore event dispatch failures to not break the flow
        }
    }
}
