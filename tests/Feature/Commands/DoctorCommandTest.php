<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Tests\Feature\Commands;

use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| DoctorCommand Feature Tests
|--------------------------------------------------------------------------
*/

it('runs breach:doctor successfully', function () {
    $exitCode = Artisan::call('breach:doctor');

    expect($exitCode)->toBe(0);
});

it('displays doctor diagnostics header', function () {
    Artisan::call('breach:doctor');

    $output = Artisan::output();

    expect($output)->toContain('Doctor')
        ->and($output)->toContain('✔');
});
