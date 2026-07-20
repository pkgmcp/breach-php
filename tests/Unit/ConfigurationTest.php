<?php

declare(strict_types=1);

use ShamimStack\BreachPHP\Config\Configuration;
use ShamimStack\BreachPHP\Exceptions\ConfigurationException;

it('creates default configuration', function (): void {
    $config = new Configuration();

    expect($config->provider())->toBe('hibp')
        ->and($config->storage())->toBe('database')
        ->and($config->cache())->toBe('array')
        ->and($config->timeout())->toBe(10)
        ->and($config->connectTimeout())->toBe(5)
        ->and($config->retries())->toBe(3)
        ->and($config->retryDelay())->toBe(250)
        ->and($config->storePrefixes())->toBeTrue()
        ->and($config->queueEnabled())->toBeFalse();
});

it('creates custom configuration', function (): void {
    $config = new Configuration(
        provider: 'hibp',
        storage: 'sqlite',
        cache: 'redis',
        timeout: 20,
        connectTimeout: 10,
        retries: 5,
        retryDelay: 500,
        storePrefixes: false,
        queueEnabled: true,
        queueConnection: 'redis',
    );

    expect($config->provider())->toBe('hibp')
        ->and($config->storage())->toBe('sqlite')
        ->and($config->cache())->toBe('redis')
        ->and($config->timeout())->toBe(20)
        ->and($config->connectTimeout())->toBe(10)
        ->and($config->retries())->toBe(5)
        ->and($config->retryDelay())->toBe(500)
        ->and($config->storePrefixes())->toBeFalse()
        ->and($config->queueEnabled())->toBeTrue()
        ->and($config->queueConnection())->toBe('redis');
});

it('creates configuration from array', function (): void {
    $config = Configuration::fromArray([
        'provider' => 'hibp',
        'storage' => 'sqlite',
        'timeout' => 15,
    ]);

    expect($config->provider())->toBe('hibp')
        ->and($config->storage())->toBe('sqlite')
        ->and($config->timeout())->toBe(15);
});

it('converts configuration to array', function (): void {
    $config = new Configuration(
        provider: 'hibp',
        timeout: 20,
    );

    $array = $config->toArray();

    expect($array)
        ->toHaveKey('provider', 'hibp')
        ->toHaveKey('timeout', 20)
        ->toHaveKey('queue')
        ->toHaveKey('store_prefixes');
});

it('rejects invalid provider', function (): void {
    Configuration::fromArray(['provider' => 'invalid']);
})->throws(ConfigurationException::class);

it('rejects invalid storage', function (): void {
    Configuration::fromArray(['storage' => 'invalid']);
})->throws(ConfigurationException::class);

it('rejects invalid cache', function (): void {
    Configuration::fromArray(['cache' => 'invalid']);
})->throws(ConfigurationException::class);

it('rejects negative timeout', function (): void {
    Configuration::fromArray(['timeout' => -1]);
})->throws(ConfigurationException::class);

it('rejects negative retries', function (): void {
    Configuration::fromArray(['retries' => -1]);
})->throws(ConfigurationException::class);

it('rejects negative retry delay', function (): void {
    Configuration::fromArray(['retry_delay' => -1]);
})->throws(ConfigurationException::class);
