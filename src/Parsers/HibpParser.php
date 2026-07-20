<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Parsers;

use ShamimStack\BreachPHP\Contracts\ParserInterface;
use ShamimStack\BreachPHP\DTO\ProviderResponse;
use ShamimStack\BreachPHP\Exceptions\ParserException;

/**
 * Parses responses from the Have I Been Pwned Pwned Passwords API.
 *
 * The API returns lines in the format: SUFFIX:COUNT
 * where SUFFIX is a 35-character SHA-1 suffix and COUNT is the breach count.
 */
final class HibpParser implements ParserInterface
{
    /**
     * Parse a raw HIBP response into a ProviderResponse DTO.
     *
     * @throws ParserException
     */
    public function parse(string $prefix, string $rawResponse): ProviderResponse
    {
        if ($rawResponse === '') {
            return new ProviderResponse(
                prefix: strtoupper($prefix),
                suffixes: [],
            );
        }

        $lines = explode("\r\n", trim($rawResponse));

        if (count($lines) === 0) {
            return new ProviderResponse(
                prefix: strtoupper($prefix),
                suffixes: [],
            );
        }

        $suffixes = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $parts = explode(':', $line, 2);

            if (count($parts) !== 2) {
                throw ParserException::invalidFormat('SUFFIX:COUNT');
            }

            $suffix = trim($parts[0]);
            $count = (int) trim($parts[1]);

            if (strlen($suffix) !== 35) {
                throw ParserException::invalidFormat(
                    "Suffix must be exactly 35 characters, got " . strlen($suffix)
                );
            }

            if ($count < 0) {
                throw ParserException::invalidFormat('Count must be non-negative');
            }

            $suffixes[] = [
                'suffix' => strtoupper($suffix),
                'count' => $count,
            ];
        }

        return new ProviderResponse(
            prefix: strtoupper($prefix),
            suffixes: $suffixes,
        );
    }
}
