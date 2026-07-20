<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Support;

/**
 * String utility methods.
 */
final class Str
{
    /**
     * Check if a string starts with a given prefix.
     */
    public static function startsWith(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_starts_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a string ends with a given suffix.
     */
    public static function endsWith(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_ends_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a string contains a given substring.
     */
    public static function contains(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert a string to uppercase.
     */
    public static function upper(string $value): string
    {
        return strtoupper($value);
    }

    /**
     * Convert a string to lowercase.
     */
    public static function lower(string $value): string
    {
        return strtolower($value);
    }

    /**
     * Generate a random string.
     */
    public static function random(int $length = 16): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $max = strlen($chars) - 1;

        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $max)];
        }

        return $result;
    }

    /**
     * Trim a string.
     */
    public static function trim(string $value, string $characters = " \t\n\r\0\x0B"): string
    {
        return trim($value, $characters);
    }

    /**
     * Left trim a string.
     */
    public static function ltrim(string $value, string $characters = " \t\n\r\0\x0B"): string
    {
        return ltrim($value, $characters);
    }

    /**
     * Right trim a string.
     */
    public static function rtrim(string $value, string $characters = " \t\n\r\0\x0B"): string
    {
        return rtrim($value, $characters);
    }

    /**
     * Limit a string to a given length.
     */
    public static function limit(string $value, int $limit = 100, string $suffix = '...'): string
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return mb_substr($value, 0, $limit, 'UTF-8') . $suffix;
    }

    /**
     * Convert a string to snake_case.
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        $value = preg_replace('/\s+/u', '', $value);

        $value = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value), 'UTF-8');

        return str_replace('-', $delimiter, $value);
    }

    /**
     * Convert a string to camelCase.
     */
    public static function camel(string $value): string
    {
        return lcfirst(self::studly($value));
    }

    /**
     * Convert a string to StudlyCase.
     */
    public static function studly(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }

    /**
     * Check if a string is empty or whitespace.
     */
    public static function blank(string $value): bool
    {
        return trim($value) === '';
    }

    /**
     * Check if a string is not empty.
     */
    public static function filled(string $value): bool
    {
        return ! self::blank($value);
    }
}
