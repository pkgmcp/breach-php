<?php

declare(strict_types=1);

use ShamimStack\BreachPHP\Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit
| test case class. By default, that class is "PHPUnit\Framework\TestCase".
|
*/

uses(TestCase::class)->in('Unit', 'Feature', 'Integration');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that certain conditions
| meet the expected behavior. Pest provides a variety of "expectations" that
| work on the tested variable. For example:
|
|   expect($result->isBreached())->toBeTrue();
|
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is out of the box very clean, it does allow you to create
| custom functions that can be used across all your tests.
|
*/

function createPasswordChecker(
    ?\ShamimStack\BreachPHP\Contracts\ProviderInterface $provider = null,
    ?\ShamimStack\BreachPHP\Contracts\StorageInterface $storage = null,
    bool $storePrefixes = true,
): \ShamimStack\BreachPHP\Services\PasswordChecker {
    $provider ??= new \ShamimStack\BreachPHP\Providers\HibpProvider(
        httpClient: new \ShamimStack\BreachPHP\Http\HttpClient(
            client: new \ShamimStack\BreachPHP\Http\FakeHttpClient(),
        ),
    );

    return new \ShamimStack\BreachPHP\Services\PasswordChecker(
        provider: $provider,
        hasher: new \ShamimStack\BreachPHP\Hash\Sha1Hasher(),
        storage: $storage,
        storePrefixes: $storePrefixes,
    );
}
