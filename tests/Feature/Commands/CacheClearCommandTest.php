<?php

declare(strict_types=1);

use Illuminate\Database\ConnectionInterface;

it('runs cache-clear command successfully', function (): void {
    $cache = \Mockery::mock(\ShamimStack\BreachPHP\Contracts\CacheInterface::class);
    $cache->shouldReceive('flush')->once();

    $this->app->instance(\ShamimStack\BreachPHP\Contracts\CacheInterface::class, $cache);

    $this->artisan('breach:cache-clear')
        ->expectsOutput('Clearing breach cache...')
        ->expectsOutput('Cache cleared successfully.')
        ->assertExitCode(0);
});

it('runs cache-clear command handles errors', function (): void {
    $cache = \Mockery::mock(\ShamimStack\BreachPHP\Contracts\CacheInterface::class);
    $cache->shouldReceive('flush')->andThrow(new \RuntimeException('Connection failed'));

    $this->app->instance(\ShamimStack\BreachPHP\Contracts\CacheInterface::class, $cache);

    $this->artisan('breach:cache-clear')
        ->expectsOutput('Clearing breach cache...')
        ->assertExitCode(1);
});
