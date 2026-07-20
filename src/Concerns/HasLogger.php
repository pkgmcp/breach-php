<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Concerns;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Provides PSR-3 logging capabilities.
 */
trait HasLogger
{
    private ?LoggerInterface $logger = null;

    /**
     * Get the logger instance.
     */
    public function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            $this->logger = $this->resolveLogger();
        }

        return $this->logger;
    }

    /**
     * Set the logger instance.
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Resolve the logger from the container or return a null logger.
     */
    private function resolveLogger(): LoggerInterface
    {
        if (function_exists('app')) {
            try {
                $logger = app('log');

                if ($logger instanceof LoggerInterface) {
                    return $logger;
                }
            } catch (\Throwable) {
                // Container not available
            }
        }

        return new NullLogger();
    }
}
