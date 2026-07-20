<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Tests\Unit;

use ShamimStack\BreachPHP\Hash\Sha1Hasher;
use ShamimStack\BreachPHP\ValueObjects\PasswordHash;
use ShamimStack\BreachPHP\ValueObjects\Prefix;
use ShamimStack\BreachPHP\ValueObjects\Suffix;

it('generates correct SHA-1 hash', function () {
    $hasher = new Sha1Hasher();

    $hash = $hasher->hash('password123');

    expect($hash)->toBe('CBFDAC6008F9CAB4083784CBD1874F76618D2A97');
});

it('extracts correct prefix', function () {
    $hasher = new Sha1Hasher();

    $prefix = $hasher->prefix('password123');

    expect($prefix)->toBe('CBFDA');
});

it('extracts correct suffix', function () {
    $hasher = new Sha1Hasher();

    $suffix = $hasher->suffix('password123');

    expect($suffix)->toBe('C6008F9CAB4083784CBD1874F76618D2A97');
});

it('creates PasswordHash value object from password', function () {
    $hasher = new Sha1Hasher();

    $hash = $hasher->createPasswordHash('password123');

    expect($hash)->toBeInstanceOf(PasswordHash::class)
        ->and($hash->value())->toBe('CBFDAC6008F9CAB4083784CBD1874F76618D2A97')
        ->and($hash->prefix())->toBe('CBFDA')
        ->and($hash->suffix())->toBe('C6008F9CAB4083784CBD1874F76618D2A97');
});

it('throws exception for empty password', function () {
    $hasher = new Sha1Hasher();

    $hasher->hash('');
})->throws(\ShamimStack\BreachPHP\Exceptions\InvalidPasswordException::class);

it('creates Prefix value object', function () {
    $prefix = Prefix::fromString('CBFDA');

    expect($prefix->value())->toBe('CBFDA')
        ->and((string) $prefix)->toBe('CBFDA');
});

it('creates Suffix value object', function () {
    $suffix = Suffix::fromString('C6008F9CAB4083784CBD1874F76618D2A97');

    expect($suffix->value())->toBe('C6008F9CAB4083784CBD1874F76618D2A97')
        ->and((string) $suffix)->toBe('C6008F9CAB4083784CBD1874F76618D2A97');
});

it('throws exception for invalid prefix length', function () {
    Prefix::fromString('CB');
})->throws(\InvalidArgumentException::class);

it('throws exception for invalid suffix length', function () {
    Suffix::fromString('SHORT');
})->throws(\InvalidArgumentException::class);
