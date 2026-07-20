<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Commands;

use Illuminate\Console\Command;
use ShamimStack\BreachPHP\Services\SyncService;

/**
 * Artisan command to synchronize breach prefixes from the provider.
 */
final class SyncCommand extends Command
{
    protected $signature = 'breach:sync
        {--prefix= : Synchronize a specific SHA-1 prefix}
        {--queue : Dispatch synchronization jobs to the queue}
        {--force : Re-download existing prefixes}';

    protected $description = 'Synchronize breach prefixes from the provider';

    public function __construct(
        private readonly SyncService $syncService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $prefix = $this->option('prefix');

        if ($prefix !== null && $prefix !== '') {
            $this->info("Synchronizing prefix: {$prefix}");

            $result = $this->syncService->syncPrefix($prefix);

            if ($result->isSuccess()) {
                $this->info("Synchronization completed successfully.");
                $this->line("Stored suffixes: {$result->storedSuffixes()}");

                return Command::SUCCESS;
            }

            $this->error("Synchronization failed: {$result->error()}");

            return Command::FAILURE;
        }

        $this->info('Synchronizing...');
        $this->line('This command synchronizes prefixes on-demand as they are checked.');
        $this->line('Use breach:check to trigger synchronization for specific prefixes.');
        $this->info('Completed.');

        return Command::SUCCESS;
    }
}
