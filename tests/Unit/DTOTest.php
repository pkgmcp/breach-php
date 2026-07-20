<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Tests\Unit;

use ShamimStack\BreachPHP\DTO\PasswordResult;
use ShamimStack\BreachPHP\DTO\PrefixResponse;
use ShamimStack\BreachPHP\DTO\ProviderResponse;
use ShamimStack\BreachPHP\DTO\StorageStatistics;
use ShamimStack\BreachPHP\DTO\HealthReport;
use ShamimStack\BreachPHP\DTO\SyncResult;
use ShamimStack\BreachPHP\Enums\HealthStatus;

it('creates safe PasswordResult', function () {
    $result = PasswordResult::safe(
        hash: 'CBFDAC6008F9CAB4083784CBD1874F76618D2A97',
        prefix: 'CBFDA',
        suffix: 'C6008F9CAB4083784CBD1874F76618D2A97',
        provider: 'hibp',
        source: 'provider',
    );

    expect($result->isBreached())->toBeFalse()
        ->and($result->isSafe())->toBeTrue()
        ->and($result->count())->toBe(0)
        ->and($result->hash())->toBe('CBFDAC6008F9CAB4083784CBD1874F76618D2A97')
        ->and($result->prefix())->toBe('CBFDA')
        ->and($result->suffix())->toBe('C6008F9CAB4083784CBD1874F76618D2A97')
        ->and($result->provider())->toBe('hibp')
        ->and($result->source())->toBe('provider');
});

it('creates breached PasswordResult', function () {
    $result = PasswordResult::breached(
        count: 123,
        hash: 'CBFDAC6008F9CAB4083784CBD1874F76618D2A97',
        prefix: 'CBFDA',
        suffix: 'C6008F9CAB4083784CBD1874F76618D2A97',
        provider: 'hibp',
        source: 'storage',
    );

    expect($result->isBreached())->toBeTrue()
        ->and($result->isSafe())->toBeFalse()
        ->and($result->count())->toBe(123);
});

it('creates ProviderResponse and finds suffix count', function () {
    $response = new ProviderResponse(
        prefix: 'CBFDA',
        suffixes: [
            ['suffix' => 'C6008F9CAB4083784CBD1874F76618D2A97', 'count' => 124532],
            ['suffix' => 'A8F3E21B8C4D5E6F7A9B0C1D2E3F4A5B6C7', 'count' => 15],
        ],
    );

    expect($response->prefix())->toBe('CBFDA')
        ->and($response->suffixCount())->toBe(2)
        ->and($response->findCount('C6008F9CAB4083784CBD1874F76618D2A97'))->toBe(124532)
        ->and($response->findCount('UNKNOWN'))->toBeNull();
});

it('creates PrefixResponse from ProviderResponse', function () {
    $providerResponse = new ProviderResponse(
        prefix: 'CBFDA',
        suffixes: [
            ['suffix' => 'C6008F9CAB4083784CBD1874F76618D2A97', 'count' => 124532],
        ],
    );

    $prefixResponse = PrefixResponse::fromProviderResponse($providerResponse);

    expect($prefixResponse->prefix())->toBe('CBFDA')
        ->and($prefixResponse->suffixCount())->toBe(1);
});

it('creates HealthReport', function () {
    $report = new HealthReport(
        status: HealthStatus::HEALTHY,
        checks: ['provider' => 'Healthy', 'storage' => 'Healthy'],
    );

    expect($report->status())->toBe(HealthStatus::HEALTHY)
        ->and($report->isHealthy())->toBeTrue()
        ->and($report->checks())->toHaveKeys(['provider', 'storage']);
});

it('creates SyncResult', function () {
    $result = new SyncResult(
        processedPrefixes: 10,
        storedPrefixes: 8,
        storedSuffixes: 1000,
        success: true,
    );

    expect($result->processedPrefixes())->toBe(10)
        ->and($result->storedPrefixes())->toBe(8)
        ->and($result->storedSuffixes())->toBe(1000)
        ->and($result->isSuccess())->toBeTrue()
        ->and($result->error())->toBeNull();
});
