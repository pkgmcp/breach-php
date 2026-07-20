<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model for the breachphp_sync_logs table.
 *
 * Tracks synchronization attempts with status, timing, and error details.
 */
final class SyncLog extends Model
{
    protected $table = 'breachphp_sync_logs';

    protected $fillable = [
        'prefix',
        'status',
        'started_at',
        'finished_at',
        'duration',
        'error',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'duration' => 'integer',
    ];

    /**
     * Mark the sync log as completed.
     */
    public function markCompleted(int $durationMs = 0): void
    {
        $this->update([
            'status' => 'completed',
            'finished_at' => now(),
            'duration' => $durationMs,
        ]);
    }

    /**
     * Mark the sync log as failed.
     */
    public function markFailed(string $error, int $durationMs = 0): void
    {
        $this->update([
            'status' => 'failed',
            'finished_at' => now(),
            'duration' => $durationMs,
            'error' => $error,
        ]);
    }
}
