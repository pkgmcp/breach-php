<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Providers;

use ShamimStack\BreachPHP\Contracts\ParserInterface;
use ShamimStack\BreachPHP\Contracts\ProviderInterface;
use ShamimStack\BreachPHP\DTO\ProviderResponse;
use ShamimStack\BreachPHP\Http\HttpClient;
use ShamimStack\BreachPHP\Parsers\HibpParser;

/**
 * Have I Been Pwned (HIBP) Pwned Passwords API provider.
 *
 * Uses the k-Anonymity model: only the first five characters of the SHA-1 hash
 * are transmitted to the API. The plaintext password is never sent.
 */
final class HibpProvider implements ProviderInterface
{
    private const BASE_URL = 'https://api.pwnedpasswords.com/range/';

    public function __construct(
        private readonly HttpClient $httpClient,
        private readonly ParserInterface $parser,
    ) {}

    /**
     * Fetch the suffix list for a given SHA-1 prefix from the HIBP API.
     */
    public function fetch(string $prefix): ProviderResponse
    {
        $url = self::BASE_URL . strtoupper($prefix);

        $rawResponse = $this->httpClient->get($url);

        return $this->parser->parse($prefix, $rawResponse);
    }
}
