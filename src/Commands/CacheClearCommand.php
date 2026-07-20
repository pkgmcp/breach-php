<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Commands;

use Illuminate\Console\Command;
use ShamimStack\BreachPHP\Contracts\CacheInterface;

/**
 * Artisan command to clear the breach cache.
 */
final class CacheClearCommand extends Command
{
    protected $signature = 'breach:cache-clear
        {--json : Output as JSON}';

    protected $description = 'Clear the breach cache';

    public function __construct(
        private readonly CacheInterface $cache,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Clearing breach cache...');

        try {
            $this->cache->flush();
            $this->info('Cache cleared successfully.');

            if ($this->option('json')) {
                $this->line(json_encode([
                    'success' => true,
                ], JSON_PRETTY_PRINT));
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to clear cache: {$e->getMessage()}");

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
