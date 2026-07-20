<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\DTO;

use ShamimStack\BreachPHP\Enums\HealthStatus;

/**
 * Immutable data transfer object containing health check results.
 */
final readonly class HealthReport
{
    /**
     * @param  array<string, string>  $checks  The individual check results (name => status).
     */
    public function __construct(
        private HealthStatus $status,
        private array $checks = [],
    ) {}

    /**
     * Get the overall health status.
     */
    public function status(): HealthStatus
    {
        return $this->status;
    }

    /**
     * Get the individual check results.
     *
     * @return array<string, string>
     */
    public function checks(): array
    {
        return $this->checks;
    }

    /**
     * Whether the overall status is healthy.
     */
    public function isHealthy(): bool
    {
        return $this->status->isHealthy();
    }
}
