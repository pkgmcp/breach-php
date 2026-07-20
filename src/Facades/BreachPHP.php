<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Facades;

use Illuminate\Support\Facades\Facade;
use ShamimStack\BreachPHP\Contracts\PasswordCheckerInterface;

/**
 * @method static \ShamimStack\BreachPHP\DTO\PasswordResult check(string $password)
 * @method static bool isSafe(string $password)
 * @method static bool isBreached(string $password)
 * @method static int count(string $password)
 *
 * @see \ShamimStack\BreachPHP\Contracts\PasswordCheckerInterface
 */
final class BreachPHP extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return PasswordCheckerInterface::class;
    }
}
