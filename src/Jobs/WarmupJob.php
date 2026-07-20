<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ShamimStack\BreachPHP\Services\WarmupService;

/**
 * Job to warmup storage with common prefixes.
 */
final class WarmupJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $timeout = 300;

    public function __construct(
        public readonly ?int $limit = null,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WarmupService $warmupService): void
    {
        $warmupService->warmup($this->limit);
    }
}
