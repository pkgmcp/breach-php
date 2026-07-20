<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Cache;

use Psr\SimpleCache\CacheInterface as Psr16CacheInterface;
use ShamimStack\BreachPHP\Contracts\CacheInterface;
use ShamimStack\BreachPHP\Enums\CacheDriver;
use ShamimStack\BreachPHP\Exceptions\ConfigurationException;

/**
 * Manages cache driver instances.
 *
 * Resolves the appropriate cache implementation based on configuration.
 */
final class CacheManager
{
    private ?CacheInterface $resolved = null;

    /**
     * @param  array{driver?: string}  $config  Cache configuration.
     * @param  Psr16CacheInterface|null  $psrCache  PSR-16 cache instance.
     */
    public function __construct(
        private readonly array $config = [],
        private readonly ?Psr16CacheInterface $psrCache = null,
    ) {}

    /**
     * Resolve the configured cache driver.
     */
    public function driver(?string $driver = null): CacheInterface
    {
        $driver ??= $this->config['driver'] ?? 'array';

        $cacheDriver = CacheDriver::tryFrom($driver);

        if ($cacheDriver === null) {
            throw ConfigurationException::driverNotSupported('cache', $driver);
        }

        return $this->resolved ??= $this->resolve($cacheDriver);
    }

    /**
     * Resolve a specific cache driver.
     */
    private function resolve(CacheDriver $driver): CacheInterface
    {
        return match ($driver) {
            CacheDriver::ARRAY => new ArrayCache(),
            CacheDriver::PSR16, CacheDriver::REDIS => $this->resolvePsr16(),
            CacheDriver::LARAVEL => $this->resolveLaravel(),
            CacheDriver::NONE => new NullCache(),
        };
    }

    /**
     * Resolve a PSR-16 cache implementation.
     */
    private function resolvePsr16(): CacheInterface
    {
        if ($this->psrCache !== null) {
            return new Psr16Cache($this->psrCache);
        }

        return new ArrayCache();
    }

    /**
     * Resolve a Laravel cache implementation.
     */
    private function resolveLaravel(): CacheInterface
    {
        if (function_exists('app')) {
            try {
                $cache = app('cache.store');

                if ($cache instanceof \Illuminate\Contracts\Cache\Repository) {
                    return new LaravelCache($cache);
                }
            } catch (\Throwable) {
                // Fall through to array cache
            }
        }

        return new ArrayCache();
    }

    /**
     * Set a specific cache driver instance (useful for testing).
     */
    public function setDriver(CacheInterface $cache): void
    {
        $this->resolved = $cache;
    }
}
