<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Events;

use ShamimStack\BreachPHP\DTO\PasswordResult;

/**
 * Dispatched when a password is not found in breach data.
 */
final readonly class PasswordSafe
{
    public function __construct(
        public PasswordResult $result,
    ) {}
}
