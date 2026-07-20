<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job to clean up old synchronization logs.
 */
final class CleanupJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 120;

    public function __construct(
        public readonly int $daysToKeep = 30,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $cutoffDate = now()->subDays($this->daysToKeep)->toDateTimeString();

        $table = 'breachphp_sync_logs';

        if (function_exists('DB')) {
            $connection = \DB::connection();

            if ($connection->getSchemaBuilder()->hasTable($table)) {
                $connection->table($table)
                    ->where('created_at', '<', $cutoffDate)
                    ->delete();
            }
        }
    }
}
