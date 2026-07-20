<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Tests\Unit;

use ShamimStack\BreachPHP\Cache\ArrayCache;
use ShamimStack\BreachPHP\Contracts\CacheInterface;

it('implements CacheInterface', function () {
    $cache = new ArrayCache();

    expect($cache)->toBeInstanceOf(CacheInterface::class);
});

it('stores and retrieves values', function () {
    $cache = new ArrayCache();

    $cache->set('key', 'value');

    expect($cache->get('key'))->toBe('value');
});

it('returns null for missing keys', function () {
    $cache = new ArrayCache();

    expect($cache->get('missing'))->toBeNull();
});

it('checks if key exists', function () {
    $cache = new ArrayCache();

    $cache->set('key', 'value');

    expect($cache->has('key'))->toBeTrue()
        ->and($cache->has('missing'))->toBeFalse();
});

it('forgets keys', function () {
    $cache = new ArrayCache();

    $cache->set('key', 'value');
    $cache->forget('key');

    expect($cache->get('key'))->toBeNull();
});

it('flushes all keys', function () {
    $cache = new ArrayCache();

    $cache->set('key1', 'value1');
    $cache->set('key2', 'value2');
    $cache->flush();

    expect($cache->get('key1'))->toBeNull()
        ->and($cache->get('key2'))->toBeNull();
});

it('respects TTL expiration', function () {
    $cache = new ArrayCache();

    $cache->set('key', 'value', -1); // Already expired

    expect($cache->get('key'))->toBeNull();
});
