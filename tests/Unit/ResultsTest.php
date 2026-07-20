<?php

declare(strict_types=1);

use ShamimStack\BreachPHP\DTO\HealthReport;
use ShamimStack\BreachPHP\DTO\SyncResult;
use ShamimStack\BreachPHP\DTO\StorageStatistics;
use ShamimStack\BreachPHP\Enums\HealthStatus;
use ShamimStack\BreachPHP\Results\HealthResult;
use ShamimStack\BreachPHP\Results\SyncResult as ResultsSyncResult;

it('creates health report dto', function (): void {
    $report = new HealthReport(
        status: HealthStatus::HEALTHY,
        checks: ['provider' => 'Healthy', 'storage' => 'Healthy'],
    );

    expect($report->status())->toBe(HealthStatus::HEALTHY)
        ->and($report->isHealthy())->toBeTrue()
        ->and($report->checks())->toHaveKeys(['provider', 'storage']);
});

it('creates health result', function (): void {
    $result = HealthResult::healthy(['provider' => 'OK']);

    expect($result->isHealthy())->toBeTrue()
        ->and($result->isUnhealthy())->toBeFalse()
        ->and($result->isDegraded())->toBeFalse()
        ->and($result->checks())->toBe(['provider' => 'OK'])
        ->and($result->error())->toBeNull();
});

it('creates unhealthy health result', function (): void {
    $result = HealthResult::unhealthy(['provider' => 'Failed'], 'Connection timeout');

    expect($result->isHealthy())->toBeFalse()
        ->and($result->isUnhealthy())->toBeTrue()
        ->and($result->error())->toBe('Connection timeout');
});

it('creates degraded health result', function (): void {
    $result = HealthResult::degraded(['storage' => 'Slow']);

    expect($result->isDegraded())->toBeTrue()
        ->and($result->toArray())->toHaveKey('status', 'degraded');
});

it('creates storage statistics', function (): void {
    $stats = new StorageStatistics(
        prefixes: 100,
        suffixes: 50000,
        databaseSize: '128 MB',
        lastSync: '2026-07-20',
    );

    expect($stats->prefixes())->toBe(100)
        ->and($stats->suffixes())->toBe(50000)
        ->and($stats->databaseSize())->toBe('128 MB')
        ->and($stats->lastSync())->toBe('2026-07-20');
});

it('creates sync result dto', function (): void {
    $result = new \ShamimStack\BreachPHP\DTO\SyncResult(
        processedPrefixes: 10,
        storedPrefixes: 8,
        storedSuffixes: 4000,
        success: true,
    );

    expect($result->processedPrefixes())->toBe(10)
        ->and($result->storedPrefixes())->toBe(8)
        ->and($result->storedSuffixes())->toBe(4000)
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->error())->toBeNull();
});

it('creates failed sync result dto', function (): void {
    $result = new \ShamimStack\BreachPHP\DTO\SyncResult(
        processedPrefixes: 5,
        storedPrefixes: 0,
        storedSuffixes: 0,
        success: false,
        error: 'Connection failed',
    );

    expect($result->isSuccess())->toBeFalse()
        ->and($result->error())->toBe('Connection failed');
});

it('creates results sync result', function (): void {
    $result = ResultsSyncResult::success(processed: 10, stored: 5, suffixes: 2500);

    expect($result->processedPrefixes())->toBe(10)
        ->and($result->storedPrefixes())->toBe(5)
        ->and($result->storedSuffixes())->toBe(2500)
        ->and($result->isSuccess())->toBeTrue();
});

it('creates failed results sync result', function (): void {
    $result = ResultsSyncResult::failed('Timeout', 3);

    expect($result->isSuccess())->toBeFalse()
        ->and($result->error())->toBe('Timeout')
        ->and($result->processedPrefixes())->toBe(3);
});
