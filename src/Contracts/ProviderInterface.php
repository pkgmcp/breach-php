<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Contracts;

use ShamimStack\BreachPHP\DTO\ProviderResponse;

/**
 * Defines the contract for external breach data providers.
 *
 * Each provider is responsible only for communicating with an external breach service.
 * Providers must not perform hashing, storage, caching, or validation.
 */
interface ProviderInterface
{
    /**
     * Fetch the suffix list for a given SHA-1 prefix from the breach provider.
     *
     * @param  string  $prefix  The five-character SHA-1 prefix.
     * @return ProviderResponse An immutable response containing the prefix and its associated suffixes.
     *
     * @throws \ShamimStack\BreachPHP\Exceptions\ApiException
     * @throws \ShamimStack\BreachPHP\Exceptions\TimeoutException
     */
    public function fetch(string $prefix): ProviderResponse;
}
