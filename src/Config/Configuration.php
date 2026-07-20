<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Config;

use ShamimStack\BreachPHP\Exceptions\ConfigurationException;

/**
 * Immutable configuration object for the BreachPHP package.
 */
final readonly class Configuration
{
    public function __construct(
        private string $provider = 'hibp',
        private string $storage = 'database',
        private string $cache = 'array',
        private int $timeout = 10,
        private int $connectTimeout = 5,
        private int $retries = 3,
        private int $retryDelay = 250,
        private bool $storePrefixes = true,
        private bool $queueEnabled = false,
        private string $queueConnection = 'default',
    ) {
        $this->validate();
    }

    /**
     * Create configuration from an array.
     *
     * @param  array<string, mixed>  $config
     */
    public static function fromArray(array $config): self
    {
        return new self(
            provider: (string) ($config['provider'] ?? 'hibp'),
            storage: (string) ($config['storage'] ?? 'database'),
            cache: (string) ($config['cache'] ?? 'array'),
            timeout: (int) ($config['timeout'] ?? 10),
            connectTimeout: (int) ($config['connect_timeout'] ?? 5),
            retries: (int) ($config['retries'] ?? 3),
            retryDelay: (int) ($config['retry_delay'] ?? 250),
            storePrefixes: (bool) ($config['store_prefixes'] ?? true),
            queueEnabled: (bool) (($config['queue']['enabled'] ?? false)),
            queueConnection: (string) (($config['queue']['connection'] ?? 'default')),
        );
    }

    public function provider(): string
    {
        return $this->provider;
    }

    public function storage(): string
    {
        return $this->storage;
    }

    public function cache(): string
    {
        return $this->cache;
    }

    public function timeout(): int
    {
        return $this->timeout;
    }

    public function connectTimeout(): int
    {
        return $this->connectTimeout;
    }

    public function retries(): int
    {
        return $this->retries;
    }

    public function retryDelay(): int
    {
        return $this->retryDelay;
    }

    public function storePrefixes(): bool
    {
        return $this->storePrefixes;
    }

    public function queueEnabled(): bool
    {
        return $this->queueEnabled;
    }

    public function queueConnection(): string
    {
        return $this->queueConnection;
    }

    /**
     * Convert configuration to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'provider' => $this->provider,
            'storage' => $this->storage,
            'cache' => $this->cache,
            'timeout' => $this->timeout,
            'connect_timeout' => $this->connectTimeout,
            'retries' => $this->retries,
            'retry_delay' => $this->retryDelay,
            'store_prefixes' => $this->storePrefixes,
            'queue' => [
                'enabled' => $this->queueEnabled,
                'connection' => $this->queueConnection,
            ],
        ];
    }

    /**
     * Validate configuration values.
     */
    private function validate(): void
    {
        if ($this->timeout <= 0) {
            throw ConfigurationException::invalidValue('timeout', $this->timeout, 'must be greater than 0');
        }

        if ($this->connectTimeout <= 0) {
            throw ConfigurationException::invalidValue('connect_timeout', $this->connectTimeout, 'must be greater than 0');
        }

        if ($this->retries < 0) {
            throw ConfigurationException::invalidValue('retries', $this->retries, 'must be 0 or greater');
        }

        if ($this->retryDelay < 0) {
            throw ConfigurationException::invalidValue('retry_delay', $this->retryDelay, 'must be 0 or greater');
        }

        $validProviders = ['hibp'];
        if (! in_array($this->provider, $validProviders, true)) {
            throw ConfigurationException::invalidValue('provider', $this->provider, 'must be one of: ' . implode(', ', $validProviders));
        }

        $validStorage = ['database', 'sqlite', 'redis', 'file', 'none'];
        if (! in_array($this->storage, $validStorage, true)) {
            throw ConfigurationException::invalidValue('storage', $this->storage, 'must be one of: ' . implode(', ', $validStorage));
        }

        $validCache = ['array', 'redis', 'psr16', 'laravel', 'none'];
        if (! in_array($this->cache, $validCache, true)) {
            throw ConfigurationException::invalidValue('cache', $this->cache, 'must be one of: ' . implode(', ', $validCache));
        }
    }
}
