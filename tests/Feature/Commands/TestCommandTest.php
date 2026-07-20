<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Tests\Feature\Commands;

use Illuminate\Support\Facades\Artisan;
use ShamimStack\BreachPHP\Tests\TestCase;

/*
|--------------------------------------------------------------------------
| TestCommand Feature Tests
|--------------------------------------------------------------------------
|
| Tests for the breach:test Artisan command verifying:
| - Command runs successfully
| - All checks pass
| - Correct output messages
|
*/

it('runs the breach:test command successfully', function () {
    $exitCode = Artisan::call('breach:test');

    expect($exitCode)->toBe(0);
});

it('displays installation checks', function () {
    Artisan::call('breach:test');

    $output = Artisan::output();

    expect($output)->toContain('Package installed')
        ->and($output)->toContain('Configuration loaded')
        ->and($output)->toContain('Storage configured')
        ->and($output)->toContain('Cache configured');
});

it('displays success message when all checks pass', function () {
    Artisan::call('breach:test');

    $output = Artisan::output();

    expect($output)->toContain('Installation successful');
});
