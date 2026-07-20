<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Events;

use ShamimStack\BreachPHP\DTO\PasswordResult;

/**
 * Dispatched after every successful password lookup.
 */
final readonly class PasswordChecked
{
    public function __construct(
        public PasswordResult $result,
    ) {}
}
