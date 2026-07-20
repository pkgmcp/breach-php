<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Enums;

/**
 * Enumerates the health status values.
 */
enum HealthStatus: string
{
    case HEALTHY = 'healthy';
    case DEGRADED = 'degraded';
    case UNHEALTHY = 'unhealthy';

    /**
     * Get the human-readable name for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::HEALTHY => 'Healthy',
            self::DEGRADED => 'Degraded',
            self::UNHEALTHY => 'Unhealthy',
        };
    }

    /**
     * Determine whether the status indicates a healthy state.
     */
    public function isHealthy(): bool
    {
        return $this === self::HEALTHY;
    }
}
