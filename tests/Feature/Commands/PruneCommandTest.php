<?php

declare(strict_types=1);

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Schema\Builder;

it('runs prune command successfully', function (): void {
    $query = \Mockery::mock();
    $query->shouldReceive('where')->andReturnSelf();
    $query->shouldReceive('delete')->andReturn(5);

    $tableQuery = \Mockery::mock();
    $tableQuery->shouldReceive('where')->andReturn($query);

    $schema = \Mockery::mock(Builder::class);
    $schema->shouldReceive('hasTable')->with('breachphp_sync_logs')->andReturn(true);

    $connection = \Mockery::mock(ConnectionInterface::class);
    $connection->shouldReceive('getSchemaBuilder')->andReturn($schema);
    $connection->shouldReceive('table')->with('breachphp_sync_logs')->andReturn($tableQuery);

    $this->app->instance(ConnectionInterface::class, $connection);

    $this->artisan('breach:prune')
        ->expectsOutput('Pruning sync logs older than 30 days...')
        ->expectsOutput('Pruned 5 log entries.')
        ->assertExitCode(0);
});

it('runs prune command with custom days', function (): void {
    $query = \Mockery::mock();
    $query->shouldReceive('where')->andReturnSelf();
    $query->shouldReceive('delete')->andReturn(10);

    $tableQuery = \Mockery::mock();
    $tableQuery->shouldReceive('where')->andReturn($query);

    $schema = \Mockery::mock(Builder::class);
    $schema->shouldReceive('hasTable')->with('breachphp_sync_logs')->andReturn(true);

    $connection = \Mockery::mock(ConnectionInterface::class);
    $connection->shouldReceive('getSchemaBuilder')->andReturn($schema);
    $connection->shouldReceive('table')->with('breachphp_sync_logs')->andReturn($tableQuery);

    $this->app->instance(ConnectionInterface::class, $connection);

    $this->artisan('breach:prune', ['--days' => 7])
        ->expectsOutput('Pruning sync logs older than 7 days...')
        ->expectsOutput('Pruned 10 log entries.')
        ->assertExitCode(0);
});
