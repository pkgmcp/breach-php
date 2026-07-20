<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Enums;

/**
 * Enumerates the synchronization status values.
 */
enum SyncStatus: string
{
    case PENDING = 'pending';
    case RUNNING = 'running';
    case SUCCESS = 'success';
    case FAILED = 'failed';

    /**
     * Get the human-readable name for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::RUNNING => 'Running',
            self::SUCCESS => 'Success',
            self::FAILED => 'Failed',
        };
    }
}
