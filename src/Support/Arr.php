<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Support;

/**
 * Array utility methods.
 */
final class Arr
{
    /**
     * Get an item from an array using "dot" notation.
     */
    public static function get(array $array, string|int|null $key, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return $array;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (explode('.', (string) $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default instanceof \Closure ? $default() : $default;
            }
        }

        return $array;
    }

    /**
     * Set an item in an array using "dot" notation.
     *
     * @param  array<string, mixed>  $array
     * @return array<string, mixed>
     */
    public static function set(array &$array, string|int|null $key, mixed $value): array
    {
        if (is_null($key)) {
            $array = $value;

            return $array;
        }

        $keys = explode('.', (string) $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Check if an item exists in an array using "dot" notation.
     */
    public static function has(array $array, string|int|null $key): bool
    {
        if (is_null($key)) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', (string) $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Remove an item from an array using "dot" notation.
     *
     * @return array<string, mixed>
     */
    public static function forget(array &$array, string|int|null $key): array
    {
        if (is_null($key)) {
            return $array;
        }

        $keys = explode('.', (string) $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($array[$key]) || ! is_array($array[$key])) {
                return $array;
            }

            $array = &$array[$key];
        }

        unset($array[array_shift($keys)]);

        return $array;
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param  array<string, mixed>  $array
     * @param  string[]|int[]  $keys
     * @return array<string, mixed>
     */
    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * Get all of the given array except for a list of keys.
     *
     * @param  array<string, mixed>  $array
     * @param  string[]|int[]  $keys
     * @return array<string, mixed>
     */
    public static function except(array $array, array $keys): array
    {
        return array_diff_key($array, array_flip($keys));
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array<int, mixed>  $array
     * @return array<int, mixed>
     */
    public static function flatten(array $array, int $depth = INF): array
    {
        $result = [];

        foreach ($array as $item) {
            if (! is_array($item) || ($depth <= 1)) {
                $result[] = $item;
            } else {
                $result = array_merge($result, self::flatten($item, $depth - 1));
            }
        }

        return $result;
    }

    /**
     * Determine if the given key exists in the provided array.
     */
    public static function exists(array $array, string|int $key): bool
    {
        return array_key_exists($key, $array);
    }

    /**
     * Get the first element of an array.
     */
    public static function first(array $array, ?callable $callback = null, mixed $default = null): mixed
    {
        if ($callback === null) {
            if (empty($array)) {
                return $default;
            }

            return reset($array);
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default instanceof \Closure ? $default() : $default;
    }

    /**
     * Get the last element of an array.
     */
    public static function last(array $array, ?callable $callback = null, mixed $default = null): mixed
    {
        if ($callback === null) {
            if (empty($array)) {
                return $default;
            }

            return end($array);
        }

        $result = $default;

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                $result = $value;
            }
        }

        return $result;
    }

    /**
     * Determine if the given value is an array.
     */
    public static function is array(mixed $value): bool
    {
        return is_array($value);
    }

    /**
     * Convert the given value to an array.
     *
     * @return array<int, mixed>
     */
    public static function wrap(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        return [$value];
    }

    /**
     * Get a value from the array, and remove it.
     */
    public static function pull(array &$array, string|int $key, mixed $default = null): mixed
    {
        $value = $array[$key] ?? $default instanceof \Closure ? $default() : $default;

        unset($array[$key]);

        return $value;
    }
}
