<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Tests\Feature\Commands;

use Illuminate\Support\Facades\Artisan;
use ShamimStack\BreachPHP\Tests\TestCase;

/*
|--------------------------------------------------------------------------
| SyncCommand Feature Tests
|--------------------------------------------------------------------------
|
| Tests for the breach:sync Artisan command verifying:
| - Command runs without prefix (general sync info)
| - Command handles missing prefix gracefully
|
*/

it('runs breach:sync without prefix option', function () {
    $exitCode = Artisan::call('breach:sync');

    expect($exitCode)->toBe(0);
});

it('displays sync information when run without prefix', function () {
    Artisan::call('breach:sync');

    $output = Artisan::output();

    expect($output)->toContain('Synchronizing')
        ->and($output)->toContain('Completed');
});

it('runs breach:sync with --prefix option', function () {
    // This will fail at the provider level since we have no real HIBP connection
    // but it should still attempt the sync and return a failure result
    $exitCode = Artisan::call('breach:sync', ['--prefix' => 'AAAAA']);

    // The command should handle the provider failure gracefully
    expect($exitCode)->toBe(1);
});
