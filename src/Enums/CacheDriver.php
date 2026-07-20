<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Enums;

/**
 * Enumerates the supported cache drivers.
 */
enum CacheDriver: string
{
    case ARRAY = 'array';
    case REDIS = 'redis';
    case PSR16 = 'psr16';
    case LARAVEL = 'laravel';
    case NONE = 'none';

    /**
     * Get the human-readable name for the cache driver.
     */
    public function label(): string
    {
        return match ($this) {
            self::ARRAY => 'Array',
            self::REDIS => 'Redis',
            self::PSR16 => 'PSR-16',
            self::LARAVEL => 'Laravel Cache',
            self::NONE => 'None',
        };
    }
}
