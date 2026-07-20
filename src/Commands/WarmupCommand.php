<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Commands;

use Illuminate\Console\Command;
use ShamimStack\BreachPHP\Services\WarmupService;

/**
 * Artisan command to warmup storage with common breach prefixes.
 */
final class WarmupCommand extends Command
{
    protected $signature = 'breach:warmup
        {--limit= : Maximum number of prefixes to warmup}
        {--json : Output as JSON}';

    protected $description = 'Warmup storage with common breach prefixes';

    public function __construct(
        private readonly WarmupService $warmupService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $syncedBefore = $this->warmupService->getSyncedCount();
        $limit = $this->option('limit') !== null
            ? (int) $this->option('limit')
            : null;

        $this->info("Synced prefixes before warmup: {$syncedBefore}");
        $this->info('Starting warmup...');

        $result = $this->warmupService->warmup($limit);

        if ($this->option('json')) {
            $this->line(json_encode([
                'processed' => $result->processedPrefixes(),
                'stored_prefixes' => $result->storedPrefixes(),
                'stored_suffixes' => $result->storedSuffixes(),
                'success' => $result->isSuccess(),
                'error' => $result->error(),
            ], JSON_PRETTY_PRINT));

            return Command::SUCCESS;
        }

        $this->info('Warmup completed.');
        $this->line("Processed: {$result->processedPrefixes()}");
        $this->line("Stored prefixes: {$result->storedPrefixes()}");
        $this->line("Stored suffixes: {$result->storedSuffixes()}");

        if (! $result->isSuccess()) {
            $this->error("Error: {$result->error()}");

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
