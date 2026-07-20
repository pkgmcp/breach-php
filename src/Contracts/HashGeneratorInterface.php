<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Contracts;

/**
 * Defines the contract for password hash generation.
 *
 * The hash generator is responsible for producing SHA-1 hashes
 * and extracting the prefix and suffix components.
 * It must never communicate with HTTP or storage.
 */
interface HashGeneratorInterface
{
    /**
     * Generate the SHA-1 hash for the given password.
     *
     * @param  string  $password  The plaintext password.
     * @return string The uppercase SHA-1 hash.
     */
    public function hash(string $password): string;

    /**
     * Extract the first five characters of the SHA-1 hash (the prefix).
     *
     * @param  string  $password  The plaintext password.
     * @return string The five-character prefix.
     */
    public function prefix(string $password): string;

    /**
     * Extract the remaining 35 characters of the SHA-1 hash (the suffix).
     *
     * @param  string  $password  The plaintext password.
     * @return string The 35-character suffix.
     */
    public function suffix(string $password): string;
}
