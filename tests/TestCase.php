<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use ShamimStack\BreachPHP\Http\FakeHttpClient;
use ShamimStack\BreachPHP\Http\RequestFactory;
use ShamimStack\BreachPHP\Providers\BreachPHPServiceProvider;

/**
 * Base test case for BreachPHP package tests.
 */
abstract class TestCase extends OrchestraTestCase
{
    /**
     * Get package providers.
     *
     * @return array<class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            BreachPHPServiceProvider::class,
        ];
    }

    /**
     * Get environment overrides for the TestCase.
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Bind fake HTTP client and PSR-17 factories for tests
        $app->singleton(ClientInterface::class, fn () => new FakeHttpClient());
        $app->singleton(RequestFactoryInterface::class, fn () => new class implements RequestFactoryInterface {
            public function createRequest(string $method, $uri): \Psr\Http\Message\RequestInterface
            {
                return new \GuzzleHttp\Psr7\Request($method, $uri);
            }
        });
        $app->singleton(RequestFactory::class, fn ($app) => new RequestFactory(
            factory: $app->make(RequestFactoryInterface::class),
        ));
    }
}
