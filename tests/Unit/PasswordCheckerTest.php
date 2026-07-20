<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Tests\Unit;

use ShamimStack\BreachPHP\Cache\ArrayCache;
use ShamimStack\BreachPHP\Contracts\ProviderInterface;
use ShamimStack\BreachPHP\Contracts\StorageInterface;
use ShamimStack\BreachPHP\DTO\ProviderResponse;
use ShamimStack\BreachPHP\Events\ApiHit;
use ShamimStack\BreachPHP\Events\CacheHit;
use ShamimStack\BreachPHP\Events\PasswordBreached;
use ShamimStack\BreachPHP\Events\PasswordChecked;
use ShamimStack\BreachPHP\Events\PasswordSafe;
use ShamimStack\BreachPHP\Events\StorageHit;
use ShamimStack\BreachPHP\Hash\Sha1Hasher;
use ShamimStack\BreachPHP\Services\PasswordChecker;
use Symfony\Component\EventDispatcher\EventDispatcher;

beforeEach(function () {
    $this->hasher = new Sha1Hasher();
    $this->cache = new ArrayCache();
});

it('returns safe result for a password not found in any source', function () {
    $hash = $this->hasher->hash('testpassword');
    $prefix = substr($hash, 0, 5);

    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->once())->method('fetch')
        ->with($prefix)
        ->willReturn(new ProviderResponse(prefix: $prefix, suffixes: []));

    $checker = new PasswordChecker(provider: $provider, hasher: $this->hasher);
    $result = $checker->check('testpassword');

    expect($result->isSafe())->toBeTrue()
        ->and($result->count())->toBe(0)
        ->and($result->source())->toBe('provider')
        ->and($result->hash())->toBe($hash);
});

it('returns breached result when provider finds the suffix', function () {
    $hash = $this->hasher->hash('password123');
    $prefix = substr($hash, 0, 5);
    $suffix = substr($hash, 5);

    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->once())->method('fetch')
        ->willReturn(new ProviderResponse(
            prefix: $prefix,
            suffixes: [['suffix' => $suffix, 'count' => 42]],
        ));

    $checker = new PasswordChecker(provider: $provider, hasher: $this->hasher);
    $result = $checker->check('password123');

    expect($result->isBreached())->toBeTrue()
        ->and($result->count())->toBe(42)
        ->and($result->source())->toBe('provider');
});

it('returns from cache when cached value exists', function () {
    $hash = $this->hasher->hash('cached_password');
    $prefix = substr($hash, 0, 5);
    $suffix = substr($hash, 5);
    $this->cache->set("breachphp:{$prefix}:{$suffix}", 100, 86400);

    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->never())->method('fetch');

    $checker = new PasswordChecker(provider: $provider, hasher: $this->hasher, cache: $this->cache);
    $result = $checker->check('cached_password');

    expect($result->isBreached())->toBeTrue()
        ->and($result->count())->toBe(100)
        ->and($result->source())->toBe('cache');
});

it('returns from storage when prefix exists locally', function () {
    $hash = $this->hasher->hash('stored_password');
    $prefix = substr($hash, 0, 5);
    $suffix = substr($hash, 5);

    $storage = $this->createMock(StorageInterface::class);
    $storage->expects($this->once())->method('find')
        ->with($prefix, $suffix)
        ->willReturn(25);

    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->never())->method('fetch');

    $checker = new PasswordChecker(provider: $provider, hasher: $this->hasher, storage: $storage, cache: $this->cache);
    $result = $checker->check('stored_password');

    expect($result->isBreached())->toBeTrue()
        ->and($result->count())->toBe(25)
        ->and($result->source())->toBe('storage');
});

it('caches result after storage hit', function () {
    $hash = $this->hasher->hash('storage_cache_test');
    $prefix = substr($hash, 0, 5);
    $suffix = substr($hash, 5);

    $storage = $this->createMock(StorageInterface::class);
    $storage->expects($this->once())->method('find')->willReturn(10);

    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->never())->method('fetch');

    $checker = new PasswordChecker(provider: $provider, hasher: $this->hasher, storage: $storage, cache: $this->cache);
    $checker->check('storage_cache_test');

    expect($this->cache->get("breachphp:{$prefix}:{$suffix}"))->toBe(10);
});

it('skips provider storage when storePrefixes is false', function () {
    $hash = $this->hasher->hash('no_store_password');
    $prefix = substr($hash, 0, 5);
    $suffix = substr($hash, 5);

    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->once())->method('fetch')
        ->willReturn(new ProviderResponse(
            prefix: $prefix,
            suffixes: [['suffix' => $suffix, 'count' => 5]],
        ));

    $storage = $this->createMock(StorageInterface::class);
    $storage->expects($this->never())->method('store');

    $checker = new PasswordChecker(provider: $provider, hasher: $this->hasher, storage: $storage, storePrefixes: false);
    $result = $checker->check('no_store_password');

    expect($result->isBreached())->toBeTrue()->and($result->count())->toBe(5);
});

it('dispatches events during provider flow', function () {
    $hash = $this->hasher->hash('event_test');
    $prefix = substr($hash, 0, 5);
    $suffix = substr($hash, 5);

    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->once())->method('fetch')
        ->willReturn(new ProviderResponse(
            prefix: $prefix,
            suffixes: [['suffix' => $suffix, 'count' => 999]],
        ));

    $dispatcher = new EventDispatcher();
    $events = [];
    $dispatcher->addListener(ApiHit::class, function () use (&$events) { $events[] = 'ApiHit'; });
    $dispatcher->addListener(PasswordChecked::class, function () use (&$events) { $events[] = 'PasswordChecked'; });
    $dispatcher->addListener(PasswordBreached::class, function () use (&$events) { $events[] = 'PasswordBreached'; });

    $checker = new PasswordChecker(provider: $provider, hasher: $this->hasher, eventDispatcher: $dispatcher);
    $result = $checker->check('event_test');

    expect($result->isBreached())->toBeTrue()
        ->and($events)->toContain('ApiHit')
        ->and($events)->toContain('PasswordChecked')
        ->and($events)->toContain('PasswordBreached');
});

it('dispatches PasswordSafe event for non-breached passwords', function () {
    $hash = $this->hasher->hash('safe_event_test');
    $prefix = substr($hash, 0, 5);

    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->once())->method('fetch')
        ->willReturn(new ProviderResponse(prefix: $prefix, suffixes: []));

    $dispatcher = new EventDispatcher();
    $safeEvents = [];
    $dispatcher->addListener(PasswordSafe::class, function () use (&$safeEvents) { $safeEvents[] = 'PasswordSafe'; });

    $checker = new PasswordChecker(provider: $provider, hasher: $this->hasher, eventDispatcher: $dispatcher);
    $result = $checker->check('safe_event_test');

    expect($result->isSafe())->toBeTrue()->and($safeEvents)->toContain('PasswordSafe');
});

it('dispatches CacheHit event on cache hit', function () {
    $hash = $this->hasher->hash('cache_event_test');
    $prefix = substr($hash, 0, 5);
    $suffix = substr($hash, 5);
    $this->cache->set("breachphp:{$prefix}:{$suffix}", 7, 86400);

    $dispatcher = new EventDispatcher();
    $cacheEvents = [];
    $dispatcher->addListener(CacheHit::class, function () use (&$cacheEvents) { $cacheEvents[] = 'CacheHit'; });

    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->never())->method('fetch');

    $checker = new PasswordChecker(provider: $provider, hasher: $this->hasher, cache: $this->cache, eventDispatcher: $dispatcher);
    $result = $checker->check('cache_event_test');

    expect($result->source())->toBe('cache')->and($cacheEvents)->toContain('CacheHit');
});

it('dispatches StorageHit event on storage hit', function () {
    $hash = $this->hasher->hash('storage_event_test');
    $prefix = substr($hash, 0, 5);
    $suffix = substr($hash, 5);

    $storage = $this->createMock(StorageInterface::class);
    $storage->expects($this->once())->method('find')->willReturn(3);

    $dispatcher = new EventDispatcher();
    $storageEvents = [];
    $dispatcher->addListener(StorageHit::class, function () use (&$storageEvents) { $storageEvents[] = 'StorageHit'; });

    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->never())->method('fetch');

    $checker = new PasswordChecker(provider: $provider, hasher: $this->hasher, storage: $storage, cache: $this->cache, eventDispatcher: $dispatcher);
    $result = $checker->check('storage_event_test');

    expect($result->source())->toBe('storage')->and($storageEvents)->toContain('StorageHit');
});

it('works without cache', function () {
    $hash = $this->hasher->hash('no_cache_test');
    $prefix = substr($hash, 0, 5);

    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->once())->method('fetch')
        ->willReturn(new ProviderResponse(prefix: $prefix, suffixes: []));

    $checker = new PasswordChecker(provider: $provider, hasher: $this->hasher, cache: null);
    $result = $checker->check('no_cache_test');

    expect($result->isSafe())->toBeTrue();
});

it('works without storage', function () {
    $hash = $this->hasher->hash('no_storage_test');
    $prefix = substr($hash, 0, 5);

    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->once())->method('fetch')
        ->willReturn(new ProviderResponse(prefix: $prefix, suffixes: []));

    $checker = new PasswordChecker(provider: $provider, hasher: $this->hasher, storage: null);
    $result = $checker->check('no_storage_test');

    expect($result->isSafe())->toBeTrue();
});

it('caches provider result for future lookups', function () {
    $hash = $this->hasher->hash('cache_after_provider');
    $prefix = substr($hash, 0, 5);
    $suffix = substr($hash, 5);

    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->once())->method('fetch')
        ->willReturn(new ProviderResponse(
            prefix: $prefix,
            suffixes: [['suffix' => $suffix, 'count' => 15]],
        ));

    $checker = new PasswordChecker(provider: $provider, hasher: $this->hasher, cache: $this->cache);

    $result1 = $checker->check('cache_after_provider');
    expect($result1->source())->toBe('provider');

    $result2 = $checker->check('cache_after_provider');
    expect($result2->source())->toBe('cache')->and($result2->count())->toBe(15);
});

it('returns safe result when suffix is not in provider response', function () {
    $hash = $this->hasher->hash('not_found_suffix');
    $prefix = substr($hash, 0, 5);

    $provider = $this->createMock(ProviderInterface::class);
    $provider->expects($this->once())->method('fetch')
        ->willReturn(new ProviderResponse(
            prefix: $prefix,
            suffixes: [['suffix' => 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'count' => 10]],
        ));

    $checker = new PasswordChecker(provider: $provider, hasher: $this->hasher);
    $result = $checker->check('not_found_suffix');

    expect($result->isSafe())->toBeTrue()->and($result->count())->toBe(0);
});
