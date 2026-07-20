<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP;

use ShamimStack\BreachPHP\Contracts\PasswordCheckerInterface;
use ShamimStack\BreachPHP\DTO\PasswordResult;

/**
 * Main entry point for the BreachPHP package.
 *
 * Provides a simple API for checking whether a password has appeared
 * in known data breaches using the HIBP k-Anonymity protocol.
 *
 * This class can be used in framework-agnostic PHP applications.
 * For Laravel, prefer the BreachPHP facade or dependency injection.
 */
final class BreachPHP implements PasswordCheckerInterface
{
    public function __construct(
        private readonly PasswordCheckerInterface $checker,
    ) {}

    /**
     * Check whether the given password exists in the configured breach database.
     */
    public function check(string $password): PasswordResult
    {
        return $this->checker->check($password);
    }

    /**
     * Check whether the given password is safe (not found in any breach).
     */
    public function isSafe(string $password): bool
    {
        return $this->check($password)->isSafe();
    }

    /**
     * Check whether the given password has been breached.
     */
    public function isBreached(string $password): bool
    {
        return $this->check($password)->isBreached();
    }

    /**
     * Get the breach count for the given password.
     */
    public function count(string $password): int
    {
        return $this->check($password)->count();
    }
}
