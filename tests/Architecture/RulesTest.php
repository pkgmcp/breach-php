<?php

declare(strict_types=1);

use NunoMaduro\Architect\Asserts\Arch;

it('ensures services do not depend on facades')
    ->expect('ShamimStack\BreachPHP\Services')
    ->not->toHaveDependencyOn('ShamimStack\BreachPHP\Facades');

it('ensures services do not depend on commands')
    ->expect('ShamimStack\BreachPHP\Services')
    ->not->toHaveDependencyOn('ShamimStack\BreachPHP\Commands');

it('ensures services do not depend on HTTP implementation')
    ->expect('ShamimStack\BreachPHP\Services')
    ->not->toHaveDependencyOn('ShamimStack\BreachPHP\Http');

it('ensures DTOs do not have business logic')
    ->expect('ShamimStack\BreachPHP\DTO')
    ->not->toHaveDependencyOn('ShamimStack\BreachPHP\Services');

it('ensures Contracts have no implementation dependencies')
    ->expect('ShamimStack\BreachPHP\Contracts')
    ->not->toHaveDependencyOn('ShamimStack\BreachPHP\Storage')
    ->not->toHaveDependencyOn('ShamimStack\BreachPHP\Providers');

it('ensures Value Objects are immutable')
    ->expect('ShamimStack\BreachPHP\ValueObjects')
    ->toBeFinal();

it('ensures Commands are final')
    ->expect('ShamimStack\BreachPHP\Commands')
    ->toBeFinal();

it('ensures Exceptions are final')
    ->expect('ShamimStack\BreachPHP\Exceptions')
    ->toBeFinal();

it('ensures Enums are in the correct namespace')
    ->expect('ShamimStack\BreachPHP\Enums')
    ->toBeFinal();
