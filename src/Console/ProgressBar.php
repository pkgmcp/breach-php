<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Console;

/**
 * Simple progress indicator for console commands.
 */
final class ProgressBar
{
    private int $current = 0;
    private int $total;
    private int $lastPercent = -1;

    public function __construct(
        private readonly int $width = 40,
    ) {}

    /**
     * Set the total number of steps.
     */
    public function setTotal(int $total): self
    {
        $this->total = max(1, $total);
        $this->current = 0;
        $this->lastPercent = -1;

        return $this;
    }

    /**
     * Advance the progress bar by one step.
     */
    public function advance(int $steps = 1): self
    {
        $this->current = min($this->current + $steps, $this->total);
        $this->render();

        return $this;
    }

    /**
     * Set the current position.
     */
    public function setCurrent(int $current): self
    {
        $this->current = min(max(0, $current), $this->total);
        $this->render();

        return $this;
    }

    /**
     * Complete the progress bar.
     */
    public function finish(): self
    {
        $this->current = $this->total;
        $this->render();
        PHP_EOL;

        return $this;
    }

    /**
     * Render the progress bar.
     */
    private function render(): void
    {
        if ($this->total <= 0) {
            return;
        }

        $percent = (int) round(($this->current / $this->total) * 100);

        if ($percent === $this->lastPercent) {
            return;
        }

        $this->lastPercent = $percent;

        $filled = (int) round($this->width * $percent / 100);
        $empty = $this->width - $filled;

        $bar = str_repeat('#', $filled) . str_repeat('-', $empty);

        fprintf(STDERR, "\r  [%s] %3d%% (%d/%d)", $bar, $percent, $this->current, $this->total);
    }
}
