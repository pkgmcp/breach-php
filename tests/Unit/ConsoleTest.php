<?php

declare(strict_types=1);

use ShamimStack\BreachPHP\Console\ProgressBar;

it('creates progress bar', function (): void {
    $bar = new ProgressBar(40);

    expect($bar)->toBeInstanceOf(ProgressBar::class);
});

it('progress bar can advance', function (): void {
    $bar = new ProgressBar(40);

    $result = $bar->setTotal(10)->advance(1);

    expect($result)->toBeInstanceOf(ProgressBar::class);
});

it('progress bar can finish', function (): void {
    $bar = new ProgressBar(40);

    $result = $bar->setTotal(10)->finish();

    expect($result)->toBeInstanceOf(ProgressBar::class);
});

it('progress bar can set current', function (): void {
    $bar = new ProgressBar(40);

    $result = $bar->setTotal(10)->setCurrent(5);

    expect($result)->toBeInstanceOf(ProgressBar::class);
});
