<?php

declare(strict_types=1);

use ShamimStack\BreachPHP\Exceptions\ApiException;
use ShamimStack\BreachPHP\Exceptions\BreachException;
use ShamimStack\BreachPHP\Exceptions\ConfigurationException;
use ShamimStack\BreachPHP\Exceptions\InvalidPasswordException;
use ShamimStack\BreachPHP\Exceptions\ParserException;
use ShamimStack\BreachPHP\Exceptions\StorageException;
use ShamimStack\BreachPHP\Exceptions\TimeoutException;

it('creates breach exception', function (): void {
    $exception = new BreachException('Test error');

    expect($exception->getMessage())->toBe('Test error')
        ->and($exception)->toBeInstanceOf(\Exception::class);
});

it('creates api exception', function (): void {
    $exception = new ApiException('API failed');

    expect($exception->getMessage())->toBe('API failed')
        ->and($exception)->toBeInstanceOf(BreachException::class);
});

it('creates timeout exception', function (): void {
    $exception = new TimeoutException('Request timed out');

    expect($exception->getMessage())->toBe('Request timed out')
        ->and($exception)->toBeInstanceOf(BreachException::class);
});

it('creates storage exception', function (): void {
    $exception = new StorageException('Storage failed');

    expect($exception->getMessage())->toBe('Storage failed')
        ->and($exception)->toBeInstanceOf(BreachException::class);
});

it('creates parser exception', function (): void {
    $exception = new ParserException('Parse error');

    expect($exception->getMessage())->toBe('Parse error')
        ->and($exception)->toBeInstanceOf(BreachException::class);
});

it('creates invalid password exception', function (): void {
    $exception = new InvalidPasswordException('Invalid password');

    expect($exception->getMessage())->toBe('Invalid password')
        ->and($exception)->toBeInstanceOf(BreachException::class);
});

it('creates configuration exception with missing key', function (): void {
    $exception = ConfigurationException::missing('timeout');

    expect($exception->getMessage())->toContain('timeout')
        ->and($exception)->toBeInstanceOf(BreachException::class);
});

it('creates configuration exception with invalid key', function (): void {
    $exception = ConfigurationException::invalid('timeout', 'must be positive');

    expect($exception->getMessage())->toContain('timeout')
        ->and($exception->getMessage())->toContain('must be positive');
});

it('creates configuration exception with driver not supported', function (): void {
    $exception = ConfigurationException::driverNotSupported('cache', 'memcached');

    expect($exception->getMessage())->toContain('cache')
        ->and($exception->getMessage())->toContain('memcached');
});

it('creates configuration exception with invalid value', function (): void {
    $exception = ConfigurationException::invalidValue('retries', -1, 'must be 0 or greater');

    expect($exception->getMessage())->toContain('retries')
        ->and($exception->getMessage())->toContain('-1')
        ->and($exception->getMessage())->toContain('must be 0 or greater');
});
