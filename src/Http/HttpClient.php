<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Http;

use Psr\Http\Client\ClientInterface;
use ShamimStack\BreachPHP\Exceptions\ApiException;
use ShamimStack\BreachPHP\Exceptions\TimeoutException;

/**
 * HTTP client for communicating with breach data providers.
 *
 * Handles request creation, response handling, timeouts, rate limiting, and retry logic.
 * This class must not contain any business logic or parsing.
 */
final class HttpClient
{
    private static int $lastRequestTime = 0;

    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactory $requestFactory,
        private readonly int $timeout = 10,
        private readonly int $retries = 3,
        private readonly int $retryDelay = 250,
        private readonly int $rateLimitMs = 1600,
    ) {}

    /**
     * Perform a GET request and return the response body.
     *
     * @throws ApiException
     * @throws TimeoutException
     */
    public function get(string $url): string
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= $this->retries; $attempt++) {
            try {
                // Rate limiting: enforce minimum delay between requests
                $this->enforceRateLimit();

                $request = $this->requestFactory->get($url);
                $response = new Response($this->client->sendRequest($request));

                // HIBP returns 404 when the prefix is not found (no breaches for that prefix)
                if ($response->isNotFound()) {
                    return '';
                }

                if ($response->isSuccessful()) {
                    return $response->body();
                }

                if ($response->isRateLimited()) {
                    // Rate limited - wait and retry
                    if ($attempt < $this->retries) {
                        usleep($this->retryDelay * 1000 * $attempt);
                        continue;
                    }
                }

                throw ApiException::invalidResponse('hibp', new \RuntimeException(
                    "HTTP {$response->statusCode()} received from provider."
                ));

            } catch (ApiException $e) {
                throw $e;
            } catch (\Throwable $e) {
                $lastException = $e;

                if ($attempt < $this->retries) {
                    usleep($this->retryDelay * 1000);
                    continue;
                }
            }
        }

        if ($lastException !== null) {
            if ($lastException instanceof \Psr\Http\Client\ConnectException
                || $lastException instanceof \GuzzleHttp\Exception\ConnectException
                || ($lastException instanceof \GuzzleHttp\Exception\RequestException
                    && str_contains($lastException->getMessage(), 'cURL error 28'))) {
                throw TimeoutException::requestTimeout($this->timeout, $lastException);
            }

            throw ApiException::providerUnavailable('hibp', $lastException);
        }

        throw ApiException::providerUnavailable('hibp');
    }

    /**
     * Get the configured timeout in seconds.
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Get the number of retry attempts.
     */
    public function getRetries(): int
    {
        return $this->retries;
    }

    /**
     * Enforce rate limiting between requests.
     *
     * HIBP API allows approximately 1 request per 1.6 seconds.
     */
    private function enforceRateLimit(): void
    {
        $now = (int) (microtime(true) * 1000);
        $elapsed = $now - self::$lastRequestTime;

        if ($elapsed < $this->rateLimitMs) {
            usleep(($this->rateLimitMs - $elapsed) * 1000);
        }

        self::$lastRequestTime = (int) (microtime(true) * 1000);
    }
}
