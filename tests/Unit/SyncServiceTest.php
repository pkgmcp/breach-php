<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Tests\Unit;

use ShamimStack\BreachPHP\Contracts\ProviderInterface;
use ShamimStack\BreachPHP\Contracts\StorageInterface;
use ShamimStack\BreachPHP\DTO\ProviderResponse;
use ShamimStack\BreachPHP\Services\SyncService;

it('syncs a single prefix successfully', function () {
    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->once())->method('fetch')
        ->with('AAAAA')
        ->willReturn(new ProviderResponse(
            prefix: 'AAAAA',
            suffixes: [
                ['suffix' => 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'count' => 5],
                ['suffix' => 'BBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBB', 'count' => 10],
            ],
        ));

    $storage = $this->createMock(StorageInterface::class);
    $storage->expects($this->once())->method('store');

    $syncService = new SyncService($provider, $storage);
    $result = $syncService->syncPrefix('aaaaa');

    expect($result->isSuccess())->toBeTrue()
        ->and($result->processedPrefixes())->toBe(1)
        ->and($result->storedPrefixes())->toBe(1)
        ->and($result->storedSuffixes())->toBe(2);
});

it('returns failure result when provider throws exception', function () {
    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->once())->method('fetch')
        ->willThrowException(new \RuntimeException('API down'));

    $storage = $this->createMock(StorageInterface::class);
    $storage->expects($this->never())->method('store');

    $syncService = new SyncService($provider, $storage);
    $result = $syncService->syncPrefix('FAIL1');

    expect($result->isSuccess())->toBeFalse()
        ->and($result->error())->toBe('API down')
        ->and($result->storedPrefixes())->toBe(0)
        ->and($result->storedSuffixes())->toBe(0);
});

it('syncs multiple prefixes successfully', function () {
    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->exactly(2))->method('fetch')
        ->willReturn(new ProviderResponse(
            prefix: 'AAAAA',
            suffixes: [['suffix' => 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'count' => 3]],
        ));

    $storage = $this->createMock(StorageInterface::class);
    $storage->expects($this->exactly(2))->method('store');

    $syncService = new SyncService($provider, $storage);
    $result = $syncService->syncPrefixes(['AAAAA', 'BBBBB']);

    expect($result->isSuccess())->toBeTrue()
        ->and($result->processedPrefixes())->toBe(2)
        ->and($result->storedPrefixes())->toBe(2)
        ->and($result->storedSuffixes())->toBe(2);
});

it('handles mixed success and failure in multiple prefix sync', function () {
    $callCount = 0;
    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->exactly(2))->method('fetch')
        ->willReturnCallback(function () use (&$callCount) {
            $callCount++;
            if ($callCount === 1) {
                return new ProviderResponse(
                    prefix: 'AAAAA',
                    suffixes: [['suffix' => 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'count' => 1]],
                );
            }
            throw new \RuntimeException('Network error');
        });

    $storage = $this->createMock(StorageInterface::class);
    $storage->expects($this->once())->method('store');

    $syncService = new SyncService($provider, $storage);
    $result = $syncService->syncPrefixes(['AAAAA', 'CCCCC']);

    expect($result->isSuccess())->toBeFalse()
        ->and($result->processedPrefixes())->toBe(2)
        ->and($result->storedPrefixes())->toBe(1)
        ->and($result->error())->toBe('Network error');
});

it('checks if prefix is synchronized', function () {
    $provider = $this->createMock(ProviderInterface::class);
    $storage = $this->createMock(StorageInterface::class);
    $storage->expects($this->once())->method('hasPrefix')
        ->with('AAAAA')
        ->willReturn(true);

    $syncService = new SyncService($provider, $storage);
    expect($syncService->isSynchronized('aaaaa'))->toBeTrue();
});

it('returns false when prefix is not synchronized', function () {
    $provider = $this->createMock(ProviderInterface::class);
    $storage = $this->createMock(StorageInterface::class);
    $storage->expects($this->once())->method('hasPrefix')
        ->with('NOTSYNCED')
        ->willReturn(false);

    $syncService = new SyncService($provider, $storage);
    expect($syncService->isSynchronized('NOTSYNCED'))->toBeFalse();
});

it('returns empty result for empty prefix list', function () {
    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->never())->method('fetch');

    $storage = $this->createMock(StorageInterface::class);
    $storage->expects($this->never())->method('store');

    $syncService = new SyncService($provider, $storage);
    $result = $syncService->syncPrefixes([]);

    expect($result->isSuccess())->toBeTrue()
        ->and($result->processedPrefixes())->toBe(0)
        ->and($result->storedPrefixes())->toBe(0)
        ->and($result->storedSuffixes())->toBe(0);
});

it('uppercases prefix before syncing', function () {
    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->once())->method('fetch')
        ->with('FFFFF')
        ->willReturn(new ProviderResponse(prefix: 'FFFFF', suffixes: []));

    $storage = $this->createMock(StorageInterface::class);
    $storage->expects($this->once())->method('store');

    $syncService = new SyncService($provider, $storage);
    $result = $syncService->syncPrefix('fffff');

    expect($result->isSuccess())->toBeTrue();
});
