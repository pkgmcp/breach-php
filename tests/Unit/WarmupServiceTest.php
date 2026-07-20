<?php

declare(strict_types=1);

use ShamimStack\BreachPHP\Contracts\ProviderInterface;
use ShamimStack\BreachPHP\Contracts\StorageInterface;
use ShamimStack\BreachPHP\DTO\ProviderResponse;
use ShamimStack\BreachPHP\Services\WarmupService;

it('creates warmup service', function (): void {
    $provider = \Mockery::mock(ProviderInterface::class);
    $storage = \Mockery::mock(StorageInterface::class);

    $service = new WarmupService($provider, $storage);

    expect($service)->toBeInstanceOf(WarmupService::class);
});

it('gets common prefixes', function (): void {
    $provider = \Mockery::mock(ProviderInterface::class);
    $storage = \Mockery::mock(StorageInterface::class);

    $service = new WarmupService($provider, $storage);
    $prefixes = $service->getCommonPrefixes();

    expect($prefixes)->toBeArray()
        ->and(count($prefixes))->toBeGreaterThan(0);
});

it('gets synced count', function (): void {
    $provider = \Mockery::mock(ProviderInterface::class);
    $storage = \Mockery::mock(StorageInterface::class);

    $storage->shouldReceive('hasPrefix')->andReturn(true);

    $service = new WarmupService($provider, $storage);

    expect($service->getSyncedCount())->toBeGreaterThan(0);
});

it('warmup skips existing prefixes', function (): void {
    $provider = \Mockery::mock(ProviderInterface::class);
    $storage = \Mockery::mock(StorageInterface::class);

    $storage->shouldReceive('hasPrefix')->andReturn(true);

    $service = new WarmupService($provider, $storage);
    $result = $service->warmup(1);

    expect($result->processedPrefixes())->toBe(0)
        ->and($result->storedPrefixes())->toBe(0);
});

it('warmup fetches new prefixes', function (): void {
    $providerResponse = new ProviderResponse(
        prefix: '00000',
        suffixes: [
            ['suffix' => 'ABC12', 'count' => 100],
            ['suffix' => 'DEF34', 'count' => 200],
        ],
    );

    $provider = \Mockery::mock(ProviderInterface::class);
    $provider->shouldReceive('fetch')->once()->andReturn($providerResponse);

    $storage = \Mockery::mock(StorageInterface::class);
    $storage->shouldReceive('hasPrefix')->once()->andReturn(false);
    $storage->shouldReceive('store')->once();

    $service = new WarmupService($provider, $storage);
    $result = $service->warmup(1);

    expect($result->processedPrefixes())->toBe(1)
        ->and($result->storedPrefixes())->toBe(1)
        ->and($result->isSuccess())->toBeTrue();
});

it('warmup handles provider errors', function (): void {
    $provider = \Mockery::mock(ProviderInterface::class);
    $provider->shouldReceive('fetch')->once()->andThrow(new \RuntimeException('Connection failed'));

    $storage = \Mockery::mock(StorageInterface::class);
    $storage->shouldReceive('hasPrefix')->once()->andReturn(false);

    $service = new WarmupService($provider, $storage);
    $result = $service->warmup(1);

    expect($result->isSuccess())->toBeFalse()
        ->and($result->error())->toContain('Connection failed');
});
