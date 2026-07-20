<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job to optimize the breach database.
 */
final class OptimizeDatabaseJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 300;

    public function __construct() {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (function_exists('DB')) {
            $connection = \DB::connection();
            $driver = $connection->getDriverName();

            if ($driver === 'sqlite') {
                $connection->statement('VACUUM');
            } elseif (in_array($driver, ['mysql', 'mariadb'])) {
                $tables = ['breachphp_prefixes', 'breachphp_suffixes'];

                foreach ($tables as $table) {
                    if ($connection->getSchemaBuilder()->hasTable($table)) {
                        $connection->statement("OPTIMIZE TABLE {$table}");
                    }
                }
            }
        }
    }
}
