<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Commands;

use Illuminate\Console\Command;
use ShamimStack\BreachPHP\Services\HealthService;

/**
 * Artisan command to display package health information.
 */
final class HealthCommand extends Command
{
    protected $signature = 'breach:health';

    protected $description = 'Display package health information';

    public function __construct(
        private readonly HealthService $healthService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $report = $this->healthService->check();

        $this->info('BreachPHP Health Check');
        $this->line('========================');
        $this->line('');

        $status = $report->status();
        $statusColor = $status->isHealthy() ? 'info' : 'error';

        $this->{"{$statusColor}"}("Overall Status: {$status->label()}");
        $this->line('');

        foreach ($report->checks() as $name => $status) {
            $this->line("  {$name}: {$status}");
        }

        return $report->isHealthy() ? Command::SUCCESS : Command::FAILURE;
    }
}
