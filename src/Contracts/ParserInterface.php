<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Contracts;

use ShamimStack\BreachPHP\DTO\ProviderResponse;

/**
 * Defines the contract for parsing raw provider responses into structured DTOs.
 *
 * Parsers convert raw HTTP responses into ProviderResponse objects.
 * They must not perform HTTP requests, access storage, or modify cache.
 */
interface ParserInterface
{
    /**
     * Parse a raw provider response into a ProviderResponse DTO.
     *
     * @param  string  $prefix  The five-character SHA-1 prefix that was queried.
     * @param  string  $rawResponse  The raw response body from the provider.
     * @return ProviderResponse The parsed response containing the prefix and suffixes.
     *
     * @throws \ShamimStack\BreachPHP\Exceptions\ParserException
     */
    public function parse(string $prefix, string $rawResponse): ProviderResponse;
}
