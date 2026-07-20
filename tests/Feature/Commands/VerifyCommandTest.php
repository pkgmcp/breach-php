<?php

declare(strict_types=1);

use Illuminate\Database\ConnectionInterface;

it('runs verify command successfully', function (): void {
    $connection = \Mockery::mock(ConnectionInterface::class);
    $schema = \Mockery::mock();
    $connection->shouldReceive('getDriverName')->andReturn('sqlite');
    $connection->shouldReceive('getSchemaBuilder')->andReturn($schema);

    $this->app->instance(ConnectionInterface::class, $connection);

    $this->artisan('breach:verify')
        ->expectsOutput('Verifying storage integrity...')
        ->expectsOutput('Storage integrity verified.')
        ->assertExitCode(0);
});
