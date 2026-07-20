<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Providers;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Illuminate\Support\ServiceProvider;
use ShamimStack\BreachPHP\Cache\CacheManager;
use ShamimStack\BreachPHP\Contracts\CacheInterface;
use ShamimStack\BreachPHP\Contracts\HashGeneratorInterface;
use ShamimStack\BreachPHP\Contracts\ParserInterface;
use ShamimStack\BreachPHP\Contracts\PasswordCheckerInterface;
use ShamimStack\BreachPHP\Contracts\ProviderInterface;
use ShamimStack\BreachPHP\Contracts\StorageInterface;
use ShamimStack\BreachPHP\Hash\Sha1Hasher;
use ShamimStack\BreachPHP\Http\HttpClient;
use ShamimStack\BreachPHP\Http\RequestFactory;
use ShamimStack\BreachPHP\Parsers\HibpParser;
use ShamimStack\BreachPHP\Providers\HibpProvider;
use ShamimStack\BreachPHP\Services\HealthService;
use ShamimStack\BreachPHP\Services\PasswordChecker;
use ShamimStack\BreachPHP\Services\StatisticsService;
use ShamimStack\BreachPHP\Services\SyncService;
use ShamimStack\BreachPHP\Services\WarmupService;
use ShamimStack\BreachPHP\Storage\DatabaseStorage;
use ShamimStack\BreachPHP\Storage\SQLiteStorage;
use ShamimStack\BreachPHP\Storage\NullStorage;

/**
 * BreachPHP Service Provider.
 *
 * Registers all package services into the Laravel service container.
 * Supports auto-discovery for Laravel 5.5+.
 */
final class BreachPHPServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/breach.php',
            'breachphp'
        );

        $this->registerContracts();
        $this->registerServices();
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->registerPublishing();
        $this->registerCommands();
    }

    /**
     * Register core contracts.
     */
    private function registerContracts(): void
    {
        $this->app->singleton(HashGeneratorInterface::class, Sha1Hasher::class);

        $this->app->singleton(ParserInterface::class, HibpParser::class);

        $this->app->singleton(CacheInterface::class, function () {
            $manager = new CacheManager(
                config: config('breachphp', []),
            );

            return $manager->driver();
        });

        $this->app->singleton(ProviderInterface::class, function ($app) {
            return new HibpProvider(
                httpClient: $app->make(HttpClient::class),
                parser: $app->make(ParserInterface::class),
            );
        });

        if (! $this->app->bound(\Psr\Http\Client\ClientInterface::class)) {
            $this->app->singleton(\Psr\Http\Client\ClientInterface::class, static function () {
                return Psr18ClientDiscovery::find();
            });
        }

        if (! $this->app->bound(\Psr\Http\Message\RequestFactoryInterface::class)) {
            $this->app->singleton(\Psr\Http\Message\RequestFactoryInterface::class, static function () {
                return Psr17FactoryDiscovery::findRequestFactory();
            });
        }

        $this->app->singleton(HttpClient::class, function ($app) {
            $config = config('breachphp', []);

            return new HttpClient(
                client: $app->make(\Psr\Http\Client\ClientInterface::class),
                requestFactory: new RequestFactory(
                    factory: $app->make(\Psr\Http\Message\RequestFactoryInterface::class),
                ),
                timeout: $config['timeout'] ?? 10,
                retries: $config['retries'] ?? 3,
                retryDelay: $config['retry_delay'] ?? 250,
            );
        });

        $this->app->singleton(StorageInterface::class, function ($app) {
            $driver = config('breachphp.storage', 'database');
            $tablePrefix = config('breachphp.table_prefix', 'breachphp_');

            return match ($driver) {
                'none' => new NullStorage(),
                'sqlite' => new SQLiteStorage(
                    connection: $app['db']->connection(),
                    tablePrefix: $tablePrefix,
                ),
                default => new DatabaseStorage(
                    connection: $app['db']->connection(),
                    tablePrefix: $tablePrefix,
                ),
            };
        });
    }

    /**
     * Register application services.
     */
    private function registerServices(): void
    {
        $this->app->singleton(PasswordCheckerInterface::class, function ($app) {
            $eventDispatcher = $app->bound(\Symfony\Component\EventDispatcher\EventDispatcherInterface::class)
                ? $app->make(\Symfony\Component\EventDispatcher\EventDispatcherInterface::class)
                : null;

            return new PasswordChecker(
                provider: $app->make(ProviderInterface::class),
                hasher: $app->make(HashGeneratorInterface::class),
                storage: $app->make(StorageInterface::class),
                cache: $app->make(CacheInterface::class),
                eventDispatcher: $eventDispatcher,
                storePrefixes: config('breachphp.store_prefixes', true),
            );
        });

        $this->app->singleton(SyncService::class, function ($app) {
            return new SyncService(
                provider: $app->make(ProviderInterface::class),
                storage: $app->make(StorageInterface::class),
            );
        });

        $this->app->singleton(HealthService::class, function ($app) {
            return new HealthService(
                provider: $app->make(ProviderInterface::class),
                storage: $app->make(StorageInterface::class),
                cache: $app->make(CacheInterface::class),
            );
        });

        $this->app->singleton(StatisticsService::class, function ($app) {
            return new StatisticsService(
                storage: $app->make(StorageInterface::class),
            );
        });

        $this->app->singleton(WarmupService::class, function ($app) {
            return new WarmupService(
                provider: $app->make(ProviderInterface::class),
                storage: $app->make(StorageInterface::class),
            );
        });
    }

    /**
     * Register package publishing.
     */
    private function registerPublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../../config/breach.php' => config_path('breach.php'),
        ], 'breachphp-config');

        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations'),
        ], 'breachphp-migrations');
    }

    /**
     * Register Artisan commands.
     */
    private function registerCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            \ShamimStack\BreachPHP\Commands\CheckCommand::class,
            \ShamimStack\BreachPHP\Commands\SyncCommand::class,
            \ShamimStack\BreachPHP\Commands\WarmupCommand::class,
            \ShamimStack\BreachPHP\Commands\HealthCommand::class,
            \ShamimStack\BreachPHP\Commands\StatsCommand::class,
            \ShamimStack\BreachPHP\Commands\TestCommand::class,
            \ShamimStack\BreachPHP\Commands\DoctorCommand::class,
            \ShamimStack\BreachPHP\Commands\VerifyCommand::class,
            \ShamimStack\BreachPHP\Commands\OptimizeCommand::class,
            \ShamimStack\BreachPHP\Commands\CacheClearCommand::class,
            \ShamimStack\BreachPHP\Commands\PruneCommand::class,
        ]);
    }
}
