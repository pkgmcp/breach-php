<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Commands;

use Illuminate\Console\Command;
use ShamimStack\BreachPHP\Contracts\StorageInterface;

/**
 * Artisan command to verify the integrity of local storage.
 */
final class VerifyCommand extends Command
{
    protected $signature = 'breach:verify
        {--json : Output as JSON}';

    protected $description = 'Verify the integrity of local storage';

    public function __construct(
        private readonly StorageInterface $storage,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Verifying storage integrity...');

        $stats = $this->storage->stats();
        $issues = [];

        // Basic validation
        if ($stats->prefixes() < 0) {
            $issues[] = 'Invalid prefix count';
        }

        if ($stats->suffixes() < 0) {
            $issues[] = 'Invalid suffix count';
        }

        $isValid = count($issues) === 0;

        if ($this->option('json')) {
            $this->line(json_encode([
                'valid' => $isValid,
                'prefixes' => $stats->prefixes(),
                'suffixes' => $stats->suffixes(),
                'database_size' => $stats->databaseSize(),
                'last_sync' => $stats->lastSync(),
                'issues' => $issues,
            ], JSON_PRETTY_PRINT));

            return $isValid ? Command::SUCCESS : Command::FAILURE;
        }

        if ($isValid) {
            $this->info('Storage integrity verified.');
        } else {
            $this->error('Storage integrity issues found:');
            foreach ($issues as $issue) {
                $this->line("  - {$issue}");
            }
        }

        $this->line('');
        $this->line("Prefixes: {$stats->prefixes()}");
        $this->line("Suffixes: {$stats->suffixes()}");

        if ($stats->databaseSize() !== null) {
            $this->line("Database size: {$stats->databaseSize()}");
        }

        if ($stats->lastSync() !== null) {
            $this->line("Last sync: {$stats->lastSync()}");
        }

        return $isValid ? Command::SUCCESS : Command::FAILURE;
    }
}
