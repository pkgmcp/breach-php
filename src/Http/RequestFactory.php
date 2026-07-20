<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Http;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Factory for creating HTTP requests with default headers.
 *
 * Wraps a PSR-17 RequestFactoryInterface and applies consistent
 * headers (User-Agent, Accept) for all breach provider requests.
 */
final class RequestFactory
{
    /** @var array<string, string> Default headers applied to every request */
    private readonly array $defaultHeaders;

    public function __construct(
        private readonly RequestFactoryInterface $factory,
        array $defaultHeaders = [],
    ) {
        $this->defaultHeaders = array_merge([
            'User-Agent' => 'BreachPHP/1.0',
            'Accept' => 'text/plain',
        ], $defaultHeaders);
    }

    /**
     * Create a GET request with default headers.
     */
    public function get(string $url): RequestInterface
    {
        return $this->create('GET', $url);
    }

    /**
     * Create a request with the given method, URL, and default headers.
     */
    public function create(string $method, string $url): RequestInterface
    {
        $request = $this->factory->createRequest($method, $url);

        foreach ($this->defaultHeaders as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        return $request;
    }

    /**
     * Get the default headers applied to every request.
     *
     * @return array<string, string>
     */
    public function getDefaultHeaders(): array
    {
        return $this->defaultHeaders;
    }
}
