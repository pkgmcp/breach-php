<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * Immutable wrapper around a PSR-7 response.
 *
 * Provides a cleaner API for accessing response data without
 * needing to interact with the full PSR-7 stream interface.
 */
final class Response
{
    private readonly string $body;
    private readonly int $statusCode;
    private readonly array $headers;

    public function __construct(
        private readonly ResponseInterface $response,
    ) {
        $this->statusCode = $response->getStatusCode();
        $this->headers = $response->getHeaders();
        $this->body = (string) $response->getBody();
    }

    /**
     * Get the response body as a string.
     */
    public function body(): string
    {
        return $this->body;
    }

    /**
     * Get the HTTP status code.
     */
    public function statusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get all response headers.
     *
     * @return array<string, list<string>>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Get a specific header value.
     */
    public function header(string $name): ?string
    {
        $lower = strtolower($name);

        foreach ($this->headers as $key => $values) {
            if (strtolower($key) === $lower) {
                return $values[0] ?? null;
            }
        }

        return null;
    }

    /**
     * Whether the response was successful (2xx status code).
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Whether the response indicates a not-found (404).
     */
    public function isNotFound(): bool
    {
        return $this->statusCode === 404;
    }

    /**
     * Whether the response indicates rate limiting (429).
     */
    public function isRateLimited(): bool
    {
        return $this->statusCode === 429;
    }

    /**
     * Get the underlying PSR-7 response.
     */
    public function psr7Response(): ResponseInterface
    {
        return $this->response;
    }
}
