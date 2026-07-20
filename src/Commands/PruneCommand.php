<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;

/**
 * Artisan command to prune old synchronization logs.
 */
final class PruneCommand extends Command
{
    protected $signature = 'breach:prune
        {--days=30 : Number of days to keep logs}
        {--json : Output as JSON}';

    protected $description = 'Prune old synchronization logs';

    public function __construct(
        private readonly ConnectionInterface $connection,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoffDate = now()->subDays($days)->toDateTimeString();

        $this->info("Pruning sync logs older than {$days} days...");

        try {
            $table = 'breachphp_sync_logs';

            if (! $this->connection->getSchemaBuilder()->hasTable($table)) {
                $this->warn('Sync logs table not found.');

                return Command::SUCCESS;
            }

            $deleted = $this->connection->table($table)
                ->where('created_at', '<', $cutoffDate)
                ->delete();

            $this->info("Pruned {$deleted} log entries.");

            if ($this->option('json')) {
                $this->line(json_encode([
                    'success' => true,
                    'deleted' => $deleted,
                    'cutoff_date' => $cutoffDate,
                ], JSON_PRETTY_PRINT));
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Prune failed: {$e->getMessage()}");

            if ($this->option('json')) {
                $this->line(json_encode([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], JSON_PRETTY_PRINT));
            }

            return Command::FAILURE;
        }
    }
}
