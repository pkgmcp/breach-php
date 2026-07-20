<?php

declare(strict_types=1);

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Schema\Builder;

it('runs optimize command for sqlite', function (): void {
    $schema = \Mockery::mock(Builder::class);
    $schema->shouldReceive('hasTable')->andReturn(false);

    $connection = \Mockery::mock(ConnectionInterface::class);
    $connection->shouldReceive('getDriverName')->andReturn('sqlite');
    $connection->shouldReceive('statement')->with('VACUUM')->once();

    $this->app->instance(ConnectionInterface::class, $connection);

    $this->artisan('breach:optimize')
        ->expectsOutput('Optimizing database...')
        ->expectsOutput('SQLite VACUUM completed.')
        ->assertExitCode(0);
});

it('runs optimize command for mysql', function (): void {
    $schema = \Mockery::mock(Builder::class);
    $schema->shouldReceive('hasTable')->with('breachphp_prefixes')->andReturn(true);
    $schema->shouldReceive('hasTable')->with('breachphp_suffixes')->andReturn(true);

    $connection = \Mockery::mock(ConnectionInterface::class);
    $connection->shouldReceive('getDriverName')->andReturn('mysql');
    $connection->shouldReceive('getSchemaBuilder')->andReturn($schema);
    $connection->shouldReceive('statement')->with('OPTIMIZE TABLE breachphp_prefixes')->once();
    $connection->shouldReceive('statement')->with('OPTIMIZE TABLE breachphp_suffixes')->once();

    $this->app->instance(ConnectionInterface::class, $connection);

    $this->artisan('breach:optimize')
        ->expectsOutput('Optimizing database...')
        ->expectsOutput('MySQL/MariaDB optimization completed.')
        ->assertExitCode(0);
});
