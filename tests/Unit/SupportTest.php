<?php

declare(strict_types=1);

use ShamimStack\BreachPHP\Config\Configuration;
use ShamimStack\BreachPHP\Rules\Breached;
use ShamimStack\BreachPHP\Rules\NotBreached;
use ShamimStack\BreachPHP\Services\WarmupService;
use ShamimStack\BreachPHP\Support\Arr;
use ShamimStack\BreachPHP\Support\Collection;
use ShamimStack\BreachPHP\Support\Str;

it('tests string utilities', function (): void {
    expect(Str::startsWith('Hello', 'He'))->toBeTrue()
        ->and(Str::startsWith('Hello', 'lo'))->toBeFalse()
        ->and(Str::endsWith('Hello', 'lo'))->toBeTrue()
        ->and(Str::endsWith('Hello', 'He'))->toBeFalse()
        ->and(Str::contains('Hello World', 'World'))->toBeTrue()
        ->and(Str::upper('hello'))->toBe('HELLO')
        ->and(Str::lower('HELLO'))->toBe('hello')
        ->and(Str::blank(''))->toBeTrue()
        ->and(Str::blank('  '))->toBeTrue()
        ->and(Str::filled('hello'))->toBeTrue()
        ->and(Str::filled(''))->toBeFalse()
        ->and(Str::limit('Hello World', 5))->toBe('Hello...')
        ->and(Str::snake('HelloWorld'))->toBe('hello_world')
        ->and(Str::camel('hello_world'))->toBe('helloWorld')
        ->and(Str::studly('hello_world'))->toBe('HelloWorld')
        ->and(Str::random(10))->toHaveLength(10);
});

it('tests array utilities', function (): void {
    $array = ['user' => ['name' => 'John']];

    expect(Arr::get($array, 'user.name'))->toBe('John')
        ->and(Arr::has($array, 'user.name'))->toBeTrue()
        ->and(Arr::has($array, 'user.email'))->toBeFalse();

    Arr::set($array, 'user.email', 'john@example.com');
    expect($array['user']['email'])->toBe('john@example.com');

    Arr::forget($array, 'user.email');
    expect(Arr::has($array, 'user.email'))->toBeFalse();

    $array2 = ['a' => 1, 'b' => 2, 'c' => 3];
    expect(Arr::only($array2, ['a', 'c']))->toBe(['a' => 1, 'c' => 3])
        ->and(Arr::except($array2, ['b']))->toBe(['a' => 1, 'c' => 3])
        ->and(Arr::flatten([[1, 2], [3, 4]]))->toBe([1, 2, 3, 4]);
});

it('tests collection operations', function (): void {
    $collection = Collection::make([1, 2, 3, 4, 5]);

    expect($collection->count())->toBe(5)
        ->and($collection->first())->toBe(1)
        ->and($collection->last())->toBe(5)
        ->and($collection->filter(fn ($n) => $n > 3)->all())->toBe([3 => 4, 4 => 5])
        ->and($collection->map(fn ($n) => $n * 2)->all())->toBe([2, 4, 6, 8, 10])
        ->and($collection->every(fn ($n) => $n > 0))->toBeTrue()
        ->and($collection->some(fn ($n) => $n > 4))->toBeTrue()
        ->and($collection->toArray())->toBe([1, 2, 3, 4, 5]);

    expect(Collection::make([])->isEmpty())->toBeTrue();
});
