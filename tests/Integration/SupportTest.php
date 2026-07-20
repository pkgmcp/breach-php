<?php

declare(strict_types=1);

use ShamimStack\BreachPHP\Support\Arr;
use ShamimStack\BreachPHP\Support\Collection;
use ShamimStack\BreachPHP\Support\Str;

it('gets values with dot notation', function (): void {
    $array = [
        'user' => [
            'name' => 'John',
            'address' => [
                'city' => 'New York',
            ],
        ],
    ];

    expect(Arr::get($array, 'user.name'))->toBe('John')
        ->and(Arr::get($array, 'user.address.city'))->toBe('New York')
        ->and(Arr::get($array, 'missing', 'default'))->toBe('default');
});

it('sets values with dot notation', function (): void {
    $array = [];

    Arr::set($array, 'user.name', 'John');

    expect($array)->toBe(['user' => ['name' => 'John']]);
});

it('checks existence with dot notation', function (): void {
    $array = [
        'user' => [
            'name' => 'John',
        ],
    ];

    expect(Arr::has($array, 'user.name'))->toBeTrue()
        ->and(Arr::has($array, 'user.email'))->toBeFalse();
});

it('gets only specified keys', function (): void {
    $array = ['name' => 'John', 'email' => 'john@example.com', 'age' => 30];

    $result = Arr::only($array, ['name', 'age']);

    expect($result)->toBe(['name' => 'John', 'age' => 30]);
});

it('gets array except specified keys', function (): void {
    $array = ['name' => 'John', 'email' => 'john@example.com', 'age' => 30];

    $result = Arr::except($array, ['email']);

    expect($result)->toBe(['name' => 'John', 'age' => 30]);
});

it('flattens nested arrays', function (): void {
    $array = [[1, 2], [3, [4, 5]]];

    expect(Arr::flatten($array))->toBe([1, 2, 3, 4, 5]);
});

it('checks string starts with', function (): void {
    expect(Str::startsWith('Hello World', 'Hello'))->toBeTrue()
        ->and(Str::startsWith('Hello World', 'World'))->toBeFalse()
        ->and(Str::startsWith('Hello World', ['Hi', 'Hello']))->toBeTrue();
});

it('checks string ends with', function (): void {
    expect(Str::endsWith('Hello World', 'World'))->toBeTrue()
        ->and(Str::endsWith('Hello World', 'Hello'))->toBeFalse()
        ->and(Str::endsWith('Hello World', ['Earth', 'World']))->toBeTrue();
});

it('checks string contains', function (): void {
    expect(Str::contains('Hello World', 'World'))->toBeTrue()
        ->and(Str::contains('Hello World', 'Earth'))->toBeFalse()
        ->and(Str::contains('Hello World', ['Earth', 'World']))->toBeTrue();
});

it('converts case correctly', function (): void {
    expect(Str::upper('hello'))->toBe('HELLO')
        ->and(Str::lower('HELLO'))->toBe('hello');
});

it('creates collection from array', function (): void {
    $collection = Collection::make([1, 2, 3, 4, 5]);

    expect($collection->count())->toBe(5)
        ->and($collection->all())->toBe([1, 2, 3, 4, 5]);
});

it('filters collection', function (): void {
    $collection = Collection::make([1, 2, 3, 4, 5]);

    $filtered = $collection->filter(fn ($item) => $item > 3);

    expect($filtered->all())->toBe([3 => 4, 4 => 5]);
});

it('maps collection', function (): void {
    $collection = Collection::make([1, 2, 3]);

    $mapped = $collection->map(fn ($item) => $item * 2);

    expect($mapped->all())->toBe([2, 4, 6]);
});

it('checks if all items pass test', function (): void {
    $collection = Collection::make([2, 4, 6, 8]);

    expect($collection->every(fn ($item) => $item % 2 === 0))->toBeTrue();

    $collection = Collection::make([2, 3, 6, 8]);

    expect($collection->every(fn ($item) => $item % 2 === 0))->toBeFalse();
});

it('checks if some items pass test', function (): void {
    $collection = Collection::make([1, 3, 5, 8]);

    expect($collection->some(fn ($item) => $item % 2 === 0))->toBeTrue()
        ->and(Collection::make([1, 3, 5])->some(fn ($item) => $item % 2 === 0))->toBeFalse();
});

it('chunks collection', function (): void {
    $collection = Collection::make([1, 2, 3, 4, 5]);

    $chunked = $collection->chunk(2);

    expect($chunked->count())->toBe(3)
        ->and($chunked->first())->toBe([0 => 1, 1 => 2]);
});
