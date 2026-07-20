<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Concerns;

use ShamimStack\BreachPHP\Config\Configuration;

/**
 * Provides access to the package configuration.
 */
trait HasConfiguration
{
    private ?Configuration $breachConfiguration = null;

    /**
     * Get the package configuration.
     */
    public function getConfiguration(): Configuration
    {
        if ($this->breachConfiguration === null) {
            $this->breachConfiguration = $this->resolveConfiguration();
        }

        return $this->breachConfiguration;
    }

    /**
     * Set the package configuration.
     */
    public function setConfiguration(Configuration $configuration): void
    {
        $this->breachConfiguration = $configuration;
    }

    /**
     * Resolve the configuration from the container or config file.
     */
    private function resolveConfiguration(): Configuration
    {
        if (function_exists('config')) {
            $config = config('breachphp', []);

            return Configuration::fromArray($config);
        }

        return new Configuration();
    }
}
