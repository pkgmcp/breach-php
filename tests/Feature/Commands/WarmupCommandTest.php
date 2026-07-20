<?php

declare(strict_types=1);

use ShamimStack\BreachPHP\Services\WarmupService;

it('runs warmup command successfully', function (): void {
    $warmupService = \Mockery::mock(WarmupService::class);
    $warmupService->shouldReceive('getSyncedCount')->andReturn(0);
    $warmupService->shouldReceive('warmup')->andReturn(
        new \ShamimStack\BreachPHP\Results\SyncResult(
            processedPrefixes: 10,
            storedPrefixes: 10,
            storedSuffixes: 500,
            success: true,
        )
    );

    $this->app->instance(WarmupService::class, $warmupService);

    $this->artisan('breach:warmup')
        ->expectsOutput('Starting warmup...')
        ->expectsOutput('Warmup completed.')
        ->assertExitCode(0);
});

it('runs warmup command with json output', function (): void {
    $warmupService = \Mockery::mock(WarmupService::class);
    $warmupService->shouldReceive('getSyncedCount')->andReturn(0);
    $warmupService->shouldReceive('warmup')->andReturn(
        new \ShamimStack\BreachPHP\Results\SyncResult(
            processedPrefixes: 5,
            storedPrefixes: 5,
            storedSuffixes: 250,
            success: true,
        )
    );

    $this->app->instance(WarmupService::class, $warmupService);

    $this->artisan('breach:warmup', ['--json' => true])
        ->assertExitCode(0);
});
