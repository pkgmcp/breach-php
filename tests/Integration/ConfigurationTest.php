<?php

declare(strict_types=1);

use ShamimStack\BreachPHP\Config\Configuration;
use ShamimStack\BreachPHP\Contracts\CacheInterface;
use ShamimStack\BreachPHP\Contracts\PasswordCheckerInterface;
use ShamimStack\BreachPHP\Contracts\ProviderInterface;
use ShamimStack\BreachPHP\Contracts\StorageInterface;
use ShamimStack\BreachPHP\Services\HealthService;

it('creates configuration from array', function (): void {
    $config = Configuration::fromArray([
        'provider' => 'hibp',
        'storage' => 'database',
        'cache' => 'array',
        'timeout' => 15,
        'connect_timeout' => 7,
        'retries' => 5,
        'retry_delay' => 500,
        'store_prefixes' => false,
        'queue' => [
            'enabled' => true,
            'connection' => 'redis',
        ],
    ]);

    expect($config->provider())->toBe('hibp')
        ->and($config->storage())->toBe('database')
        ->and($config->cache())->toBe('array')
        ->and($config->timeout())->toBe(15)
        ->and($config->connectTimeout())->toBe(7)
        ->and($config->retries())->toBe(5)
        ->and($config->retryDelay())->toBe(500)
        ->and($config->storePrefixes())->toBeFalse()
        ->and($config->queueEnabled())->toBeTrue()
        ->and($config->queueConnection())->toBe('redis');
});

it('converts configuration to array', function (): void {
    $config = new Configuration(
        provider: 'hibp',
        timeout: 20,
    );

    $array = $config->toArray();

    expect($array)
        ->toHaveKey('provider')
        ->toHaveKey('storage')
        ->toHaveKey('cache')
        ->toHaveKey('timeout')
        ->toHaveKey('queue');
});

it('validates invalid provider', function (): void {
    Configuration::fromArray([
        'provider' => 'invalid',
    ]);
})->throws(\ShamimStack\BreachPHP\Exceptions\ConfigurationException::class);

it('validates invalid storage', function (): void {
    Configuration::fromArray([
        'storage' => 'invalid',
    ]);
})->throws(\ShamimStack\BreachPHP\Exceptions\ConfigurationException::class);

it('validates invalid cache', function (): void {
    Configuration::fromArray([
        'cache' => 'invalid',
    ]);
})->throws(\ShamimStack\BreachPHP\Exceptions\ConfigurationException::class);

it('validates negative timeout', function (): void {
    Configuration::fromArray([
        'timeout' => -1,
    ]);
})->throws(\ShamimStack\BreachPHP\Exceptions\ConfigurationException::class);

it('validates negative retries', function (): void {
    Configuration::fromArray([
        'retries' => -1,
    ]);
})->throws(\ShamimStack\BreachPHP\Exceptions\ConfigurationException::class);
