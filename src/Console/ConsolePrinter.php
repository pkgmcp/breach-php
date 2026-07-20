<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Console;

/**
 * Utility for formatting console output.
 */
final class ConsolePrinter
{
    private const SYMBOLS = [
        'success' => '+',
        'error' => 'x',
        'warning' => '!',
        'info' => '*',
    ];

    /**
     * Print a success message.
     */
    public static function success(string $message): void
    {
        self::print('success', $message);
    }

    /**
     * Print an error message.
     */
    public static function error(string $message): void
    {
        self::print('error', $message);
    }

    /**
     * Print a warning message.
     */
    public static function warning(string $message): void
    {
        self::print('warning', $message);
    }

    /**
     * Print an info message.
     */
    public static function info(string $message): void
    {
        self::print('info', $message);
    }

    /**
     * Print a key-value pair.
     */
    public static function kv(string $key, string|int|float|bool|null $value): void
    {
        $formattedValue = match (true) {
            is_bool($value) => $value ? 'Yes' : 'No',
            is_null($value) => 'N/A',
            default => (string) $value,
        };

        echo "  {$key}: {$formattedValue}" . PHP_EOL;
    }

    /**
     * Print a section header.
     */
    public static function header(string $title): void
    {
        echo PHP_EOL;
        echo str_repeat('=', strlen($title) + 4) . PHP_EOL;
        echo "  {$title}" . PHP_EOL;
        echo str_repeat('=', strlen($title) + 4) . PHP_EOL;
        echo PHP_EOL;
    }

    /**
     * Print a formatted line.
     */
    private static function print(string $type, string $message): void
    {
        $symbol = self::SYMBOLS[$type] ?? '*';
        echo "  [{$symbol}] {$message}" . PHP_EOL;
    }
}
