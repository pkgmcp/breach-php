<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ShamimStack\BreachPHP\Services\SyncService;

/**
 * Queue job for synchronizing a single prefix from the provider.
 */
final class SyncPrefixJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public readonly string $prefix,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SyncService $syncService): void
    {
        $syncService->syncPrefix($this->prefix);
    }
}
