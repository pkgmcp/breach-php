<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Results;

use ShamimStack\BreachPHP\Enums\HealthStatus;

/**
 * Immutable result object for health check operations.
 */
final readonly class HealthResult
{
    public function __construct(
        private HealthStatus $status,
        private array $checks,
        private ?string $error = null,
    ) {}

    /**
     * Create a healthy result.
     */
    public static function healthy(array $checks = []): self
    {
        return new self(
            status: HealthStatus::HEALTHY,
            checks: $checks,
        );
    }

    /**
     * Create an unhealthy result.
     */
    public static function unhealthy(array $checks = [], ?string $error = null): self
    {
        return new self(
            status: HealthStatus::UNHEALTHY,
            checks: $checks,
            error: $error,
        );
    }

    /**
     * Create a degraded result.
     */
    public static function degraded(array $checks = [], ?string $error = null): self
    {
        return new self(
            status: HealthStatus::DEGRADED,
            checks: $checks,
            error: $error,
        );
    }

    /**
     * Get the overall health status.
     */
    public function status(): HealthStatus
    {
        return $this->status;
    }

    /**
     * Check if the package is healthy.
     */
    public function isHealthy(): bool
    {
        return $this->status === HealthStatus::HEALTHY;
    }

    /**
     * Check if the package is unhealthy.
     */
    public function isUnhealthy(): bool
    {
        return $this->status === HealthStatus::UNHEALTHY;
    }

    /**
     * Check if the package is degraded.
     */
    public function isDegraded(): bool
    {
        return $this->status === HealthStatus::DEGRADED;
    }

    /**
     * Get the individual check results.
     *
     * @return array<string, mixed>
     */
    public function checks(): array
    {
        return $this->checks;
    }

    /**
     * Get the error message if unhealthy.
     */
    public function error(): ?string
    {
        return $this->error;
    }

    /**
     * Convert to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'status' => $this->status->value,
            'healthy' => $this->isHealthy(),
            'checks' => $this->checks,
            'error' => $this->error,
        ];
    }
}
