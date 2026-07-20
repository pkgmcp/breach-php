<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Commands;

use Illuminate\Console\Command;
use ShamimStack\BreachPHP\Services\HealthService;

/**
 * Artisan command to run comprehensive diagnostics.
 */
final class DoctorCommand extends Command
{
    protected $signature = 'breach:doctor';

    protected $description = 'Run diagnostics and detect configuration issues';

    public function __construct(
        private readonly HealthService $healthService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('BreachPHP Doctor');
        $this->line('========================');
        $this->line('');

        $report = $this->healthService->check();

        $checks = $report->checks();

        foreach ($checks as $name => $status) {
            $isHealthy = str_starts_with($status, 'Healthy') || str_starts_with($status, 'Not configured');
            $icon = $isHealthy ? '✔' : '✖';

            if ($isHealthy) {
                $this->info("  {$icon} {$name}: {$status}");
            } else {
                $this->error("  {$icon} {$name}: {$status}");
            }
        }

        $this->line('');

        if ($report->isHealthy()) {
            $this->info('No issues detected.');

            return Command::SUCCESS;
        }

        $this->warn('Some issues were detected. Please review the output above.');

        return Command::FAILURE;
    }
}
