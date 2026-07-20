<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Http;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Fake HTTP client for testing purposes.
 *
 * Returns predictable responses without making real network requests.
 * This class is used exclusively in the test suite.
 */
final class FakeHttpClient implements ClientInterface
{
    /** @var array<string, string> */
    private array $responses = [];

    private int $defaultStatusCode = 200;

    /**
     * Set a fake response for a specific URL pattern.
     */
    public function setResponse(string $url, string $body, int $statusCode = 200): void
    {
        $this->responses[$url] = $body;
        $this->defaultStatusCode = $statusCode;
    }

    /**
     * Send a PSR-7 request.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $url = (string) $request->getUri();

        $body = $this->responses[$url] ?? '';
        $statusCode = isset($this->responses[$url]) ? 200 : $this->defaultStatusCode;

        return new class($body, $statusCode) implements ResponseInterface {
            public function __construct(
                private readonly string $body,
                private readonly int $statusCode,
            ) {}

            public function getProtocolVersion(): string { return '1.1'; }
            public function withProtocolVersion($version): static { return $this; }
            public function getHeaders(): array { return []; }
            public function hasHeader($name): bool { return false; }
            public function getHeader($name): array { return []; }
            public function getHeaderLine($name): string { return ''; }
            public function withHeader($name, $value): static { return $this; }
            public function withoutHeader($name): static { return $this; }
            public function getBody(): StreamInterface { return new class($this->body) implements StreamInterface {
                public function __construct(private readonly string $content) {}
                public function __toString(): string { return $this->content; }
                public function getContents(): string { return $this->content; }
                public function getMetadata($key = null): mixed { return null; }
                public function isReadable(): bool { return true; }
                public function isWritable(): bool { return false; }
                public function close(): void {}
                public function detach() { return null; }
                public function getSize(): ?int { return strlen($this->content); }
                public function isSeekable(): bool { return true; }
                public function seek($offset, $whence = SEEK_SET): void {}
                public function rewind(): void {}
                public function read($length): string|false { return substr($this->content, 0, $length); }
                public function write($string): int|false { return false; }
                public function setContents($string): int|false { return false; }
                public function getStream($string, $offset = 0, $whence = SEEK_SET) { return $this; }
            }; }
            public function withBody(StreamInterface $body): static { return $this; }
            public function getStatusCode(): int { return $this->statusCode; }
            public function withStatus($code, $reasonPhrase = ''): static { return $this; }
            public function getReasonPhrase(): string { return ''; }
        };
    }
}
