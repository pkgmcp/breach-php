<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Tests\Feature\Commands;

use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| StatsCommand Feature Tests
|--------------------------------------------------------------------------
|
| Tests for breach:stats Artisan command.
|
*/

it('runs breach:stats successfully', function () {
    $exitCode = Artisan::call('breach:stats');

    expect($exitCode)->toBe(0);
});

it('displays statistics header', function () {
    Artisan::call('breach:stats');

    $output = Artisan::output();

    expect($output)->toContain('Storage Statistics')
        ->and($output)->toContain('Prefixes Stored')
        ->and($output)->toContain('Suffixes Stored');
});
