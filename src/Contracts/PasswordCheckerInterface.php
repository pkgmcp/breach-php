<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Contracts;

use ShamimStack\BreachPHP\DTO\PasswordResult;

/**
 * Defines the contract for checking whether a password has appeared in known data breaches.
 *
 * Implementations should never transmit the plaintext password over the network.
 * Only the first five characters of the SHA-1 hash (the k-Anonymity prefix) may be sent to the provider.
 */
interface PasswordCheckerInterface
{
    /**
     * Check whether the given password exists in the configured breach database.
     *
     * @param  string  $password  The plaintext password to check.
     * @return PasswordResult An immutable result containing the breach status, count, and metadata.
     */
    public function check(string $password): PasswordResult;
}
