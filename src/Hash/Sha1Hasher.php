<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Hash;

use ShamimStack\BreachPHP\Contracts\HashGeneratorInterface;
use ShamimStack\BreachPHP\ValueObjects\PasswordHash;

/**
 * Generates SHA-1 hashes for password breach detection.
 *
 * This class follows the k-Anonymity model: it never transmits plaintext passwords.
 * SHA-1 is used exclusively for the HIBP Pwned Passwords API lookup protocol,
 * not for password storage or authentication.
 */
final class Sha1Hasher implements HashGeneratorInterface
{
    /**
     * Generate the SHA-1 hash for the given password.
     */
    public function hash(string $password): string
    {
        if ($password === '') {
            throw \ShamimStack\BreachPHP\Exceptions\InvalidPasswordException::emptyPassword();
        }

        return strtoupper(sha1($password));
    }

    /**
     * Extract the first five characters of the SHA-1 hash (prefix).
     */
    public function prefix(string $password): string
    {
        return substr($this->hash($password), 0, 5);
    }

    /**
     * Extract the remaining 35 characters of the SHA-1 hash (suffix).
     */
    public function suffix(string $password): string
    {
        return substr($this->hash($password), 5);
    }

    /**
     * Create a PasswordHash value object from the given password.
     */
    public function createPasswordHash(string $password): PasswordHash
    {
        return PasswordHash::fromPassword($password);
    }
}
