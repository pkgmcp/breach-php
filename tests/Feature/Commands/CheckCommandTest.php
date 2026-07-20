<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Tests\Feature\Commands;

use Illuminate\Support\Facades\Artisan;
use ShamimStack\BreachPHP\Tests\TestCase;

/*
|--------------------------------------------------------------------------
| CheckCommand Feature Tests
|--------------------------------------------------------------------------
|
| Tests for the breach:check Artisan command verifying:
| - Command runs with --password option
| - JSON output mode
| - Failure on empty password
|
*/

it('runs breach:check with --password option', function () {
    $exitCode = Artisan::call('breach:check', ['--password' => 'testpassword']);

    expect($exitCode)->toBe(0);
});

it('outputs JSON when --json flag is set', function () {
    Artisan::call('breach:check', [
        '--password' => 'testpassword',
        '--json' => true,
    ]);

    $output = Artisan::output();
    $decoded = json_decode($output, true);

    expect($decoded)->toBeArray()
        ->and($decoded)->toHaveKeys(['breached', 'count', 'source', 'provider']);
});

it('displays error when no password is provided', function () {
    // In test context, secret() returns null, so the command should fail
    Artisan::call('breach:check');

    $output = Artisan::output();

    expect($output)->toContain('No password provided');
});

it('displays breached status correctly', function () {
    Artisan::call('breach:check', [
        '--password' => 'testpassword',
        '--json' => true,
    ]);

    $output = Artisan::output();
    $decoded = json_decode($output, true);

    // The result should have a boolean 'breached' field
    expect($decoded['breached'])->toBeBool()
        ->and($decoded['source'])->toBeString()
        ->and($decoded['provider'])->toBe('hibp');
});
