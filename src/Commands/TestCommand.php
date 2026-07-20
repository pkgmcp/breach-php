<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Commands;

use Illuminate\Console\Command;

/**
 * Artisan command to verify package installation.
 */
final class TestCommand extends Command
{
    protected $signature = 'breach:test';

    protected $description = 'Verify package installation';

    public function handle(): int
    {
        $checks = [
            'Package installed' => true,
            'Configuration loaded' => config('breachphp') !== null,
            'HTTP client available' => class_exists(\Psr\Http\Client\ClientInterface::class),
            'Storage configured' => config('breachphp.storage') !== null,
            'Cache configured' => config('breachphp.cache') !== null,
        ];

        $allPassed = true;

        foreach ($checks as $name => $passed) {
            if ($passed) {
                $this->info("  ✔ {$name}");
            } else {
                $this->error("  ✖ {$name}");
                $allPassed = false;
            }
        }

        $this->line('');

        if ($allPassed) {
            $this->info('Installation successful.');

            return Command::SUCCESS;
        }

        $this->error('Installation has issues. Please check the configuration.');

        return Command::FAILURE;
    }
}
