<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Services;

use ShamimStack\BreachPHP\Contracts\CacheInterface;
use ShamimStack\BreachPHP\Contracts\ProviderInterface;
use ShamimStack\BreachPHP\Contracts\StorageInterface;
use ShamimStack\BreachPHP\DTO\HealthReport;
use ShamimStack\BreachPHP\Enums\HealthStatus;

/**
 * Provides health monitoring and diagnostics for the BreachPHP package.
 */
final class HealthService
{
    public function __construct(
        private readonly ProviderInterface $provider,
        private readonly ?StorageInterface $storage = null,
        private readonly ?CacheInterface $cache = null,
    ) {}

    /**
     * Run a comprehensive health check.
     */
    public function check(): HealthReport
    {
        $checks = [];
        $overallStatus = HealthStatus::HEALTHY;

        // Check provider connectivity
        try {
            $this->provider->fetch('00000');
            $checks['provider'] = 'Healthy';
        } catch (\Throwable $e) {
            $checks['provider'] = 'Unhealthy: ' . $e->getMessage();
            $overallStatus = HealthStatus::UNHEALTHY;
        }

        // Check storage
        if ($this->storage !== null) {
            try {
                $this->storage->stats();
                $checks['storage'] = 'Healthy';
            } catch (\Throwable $e) {
                $checks['storage'] = 'Unhealthy: ' . $e->getMessage();
                $overallStatus = HealthStatus::DEGRADED;
            }
        } else {
            $checks['storage'] = 'Not configured';
        }

        // Check cache
        if ($this->cache !== null) {
            try {
                $testKey = 'breachphp:health:test';
                $this->cache->set($testKey, true, 60);
                $this->cache->forget($testKey);
                $checks['cache'] = 'Healthy';
            } catch (\Throwable $e) {
                $checks['cache'] = 'Unhealthy: ' . $e->getMessage();
                $overallStatus = HealthStatus::DEGRADED;
            }
        } else {
            $checks['cache'] = 'Not configured';
        }

        return new HealthReport(
            status: $overallStatus,
            checks: $checks,
        );
    }
}
