<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Commands;

use Illuminate\Console\Command;
use ShamimStack\BreachPHP\Services\StatisticsService;

/**
 * Artisan command to display storage statistics.
 */
final class StatsCommand extends Command
{
    protected $signature = 'breach:stats';

    protected $description = 'Show storage and synchronization statistics';

    public function __construct(
        private readonly StatisticsService $statsService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $stats = $this->statsService->getStats();

        $this->info('BreachPHP Storage Statistics');
        $this->line('========================');
        $this->line('');
        $this->line('  Prefixes Stored: ' . number_format($stats->prefixes()));
        $this->line('  Suffixes Stored: ' . number_format($stats->suffixes()));

        if ($stats->databaseSize() !== null) {
            $this->line('  Database Size: ' . $stats->databaseSize());
        }

        if ($stats->lastSync() !== null) {
            $this->line('  Last Sync: ' . $stats->lastSync());
        }

        return Command::SUCCESS;
    }
}
