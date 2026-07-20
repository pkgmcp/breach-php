<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Enums;

/**
 * Enumerates the supported storage drivers.
 */
enum StorageType: string
{
    case DATABASE = 'database';
    case SQLITE = 'sqlite';
    case REDIS = 'redis';
    case FILE = 'file';
    case NONE = 'none';

    /**
     * Get the human-readable name for the storage type.
     */
    public function label(): string
    {
        return match ($this) {
            self::DATABASE => 'Database',
            self::SQLITE => 'SQLite',
            self::REDIS => 'Redis',
            self::FILE => 'File',
            self::NONE => 'None',
        };
    }
}
