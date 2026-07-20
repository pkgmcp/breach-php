<?php

declare(strict_types=1);

use ShamimStack\BreachPHP\Cache\ArrayCache;
use ShamimStack\BreachPHP\Contracts\CacheInterface;

it('stores and retrieves values', function (): void {
    $cache = new ArrayCache();

    $cache->set('key1', 'value1', 60);

    expect($cache->get('key1'))->toBe('value1');
});

it('returns default for missing keys', function (): void {
    $cache = new ArrayCache();

    expect($cache->get('missing', 'default'))->toBe('default');
});

it('checks if key exists', function (): void {
    $cache = new ArrayCache();

    $cache->set('exists', 'value', 60);

    expect($cache->has('exists'))->toBeTrue()
        ->and($cache->has('missing'))->toBeFalse();
});

it('forgets keys', function (): void {
    $cache = new ArrayCache();

    $cache->set('key', 'value', 60);
    $cache->forget('key');

    expect($cache->get('key'))->toBeNull();
});

it('clears all values', function (): void {
    $cache = new ArrayCache();

    $cache->set('key1', 'value1', 60);
    $cache->set('key2', 'value2', 60);
    $cache->flush();

    expect($cache->get('key1'))->toBeNull()
        ->and($cache->get('key2'))->toBeNull();
});

it('handles multiple data types', function (): void {
    $cache = new ArrayCache();

    $cache->set('string', 'hello', 60);
    $cache->set('int', 42, 60);
    $cache->set('float', 3.14, 60);
    $cache->set('bool', true, 60);
    $cache->set('array', [1, 2, 3], 60);
    $cache->set('null', null, 60);

    expect($cache->get('string'))->toBe('hello')
        ->and($cache->get('int'))->toBe(42)
        ->and($cache->get('float'))->toBe(3.14)
        ->and($cache->get('bool'))->toBeTrue()
        ->and($cache->get('array'))->toBe([1, 2, 3]);
});
