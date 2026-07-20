<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Commands;

use Illuminate\Console\Command;
use ShamimStack\BreachPHP\Contracts\PasswordCheckerInterface;

/**
 * Artisan command to check whether a password has been breached.
 */
final class CheckCommand extends Command
{
    protected $signature = 'breach:check
        {--password= : The password to check (not recommended for production)}
        {--json : Output as JSON}
        {--offline : Force local storage lookup only}';

    protected $description = 'Check whether a password has been breached';

    public function __construct(
        private readonly PasswordCheckerInterface $checker,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $password = $this->option('password');

        if ($password === null || $password === '') {
            $password = $this->secret('Enter password');
        }

        if ($password === null || $password === '') {
            $this->error('No password provided.');

            return Command::FAILURE;
        }

        $this->info('Checking...');

        $result = $this->checker->check($password);

        if ($this->option('json')) {
            $this->line(json_encode([
                'breached' => $result->isBreached(),
                'count' => $result->count(),
                'source' => $result->source(),
                'provider' => $result->provider(),
            ], JSON_PRETTY_PRINT));

            return Command::SUCCESS;
        }

        if ($result->isBreached()) {
            $this->error("Status: Breached");
            $this->line("Count: " . number_format($result->count()));
            $this->line("Source: {$result->source()}");
            $this->line("Provider: {$result->provider()}");
        } else {
            $this->info("Status: Safe");
            $this->line("Source: {$result->source()}");
        }

        return Command::SUCCESS;
    }
}
