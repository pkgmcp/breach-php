<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Tests\Feature\Commands;

use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| HealthCommand Feature Tests
|--------------------------------------------------------------------------
*/

it('runs breach:health successfully', function () {
    $exitCode = Artisan::call('breach:health');

    expect($exitCode)->toBe(0);
});

it('displays health check header', function () {
    Artisan::call('breach:health');

    $output = Artisan::output();

    expect($output)->toContain('Health Check')
        ->and($output)->toContain('Overall Status');
});
