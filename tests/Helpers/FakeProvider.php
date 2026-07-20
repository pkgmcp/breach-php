<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Tests\Helpers;

use ShamimStack\BreachPHP\Contracts\CacheInterface;
use ShamimStack\BreachPHP\DTO\PrefixResponse;
use ShamimStack\BreachPHP\DTO\ProviderResponse;

/**
 * Test helper for creating fake providers and responses.
 */
final class FakeProvider
{
    private array $responses = [];
    private int $callCount = 0;

    /**
     * Set up a canned response for a prefix.
     */
    public function whenPrefix(string $prefix, array $suffixes): self
    {
        $this->responses[strtoupper($prefix)] = $suffixes;

        return $this;
    }

    /**
     * Create a fake ProviderResponse.
     */
    public static function createResponse(
        string $prefix,
        array $suffixes,
    ): ProviderResponse {
        return new ProviderResponse(
            body: self::formatSuffixes($suffixes),
            statusCode: 200,
        );
    }

    /**
     * Create a fake PrefixResponse.
     */
    public static function createPrefixResponse(
        string $prefix,
        int $count = 100,
    ): PrefixResponse {
        $suffixes = [];

        for ($i = 0; $i < $count; $i++) {
            $suffixes[] = [
                'suffix' => strtoupper(substr(md5((string) $i), 5)),
                'count' => random_int(1, 10000),
            ];
        }

        return new PrefixResponse(
            prefix: strtoupper($prefix),
            suffixes: $suffixes,
        );
    }

    /**
     * Format suffixes into HIBP-style response body.
     */
    private static function formatSuffixes(array $suffixes): string
    {
        $lines = [];

        foreach ($suffixes as $suffix => $count) {
            $lines[] = strtoupper($suffix) . ':' . $count;
        }

        return implode("\r\n", $lines);
    }
}
