<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\ConnectionInterface;

/**
 * Artisan command to optimize the breach database.
 */
final class OptimizeCommand extends Command
{
    protected $signature = 'breach:optimize
        {--json : Output as JSON}';

    protected $description = 'Optimize the breach database tables';

    public function __construct(
        private readonly ConnectionInterface $connection,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Optimizing database...');

        $optimized = false;

        try {
            $driver = $this->connection->getDriverName();

            if ($driver === 'sqlite') {
                $this->connection->statement('VACUUM');
                $this->info('SQLite VACUUM completed.');
                $optimized = true;
            } elseif (in_array($driver, ['mysql', 'mariadb'])) {
                $tables = ['breachphp_prefixes', 'breachphp_suffixes'];
                foreach ($tables as $table) {
                    if ($this->connection->getSchemaBuilder()->hasTable($table)) {
                        $this->connection->statement("OPTIMIZE TABLE {$table}");
                    }
                }
                $this->info('MySQL/MariaDB optimization completed.');
                $optimized = true;
            } elseif ($driver === 'pgsql') {
                $this->info('PostgreSQL does not require manual optimization.');
                $optimized = true;
            }
        } catch (\Throwable $e) {
            $this->error("Optimization failed: {$e->getMessage()}");

            if ($this->option('json')) {
                $this->line(json_encode([
                    'success' => false,
                    'error' => $e->getMessage(),
                ], JSON_PRETTY_PRINT));
            }

            return Command::FAILURE;
        }

        if ($this->option('json')) {
            $this->line(json_encode([
                'success' => $optimized,
                'driver' => $this->connection->getDriverName(),
            ], JSON_PRETTY_PRINT));
        }

        return Command::SUCCESS;
    }
}
