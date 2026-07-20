<?php

declare(strict_types=1);

namespace ShamimStack\BreachPHP\Support;

/**
 * Immutable collection of items.
 *
 * @template T
 */
final class Collection implements \IteratorAggregate, \Countable
{
    /**
     * @param  T[]  $items
     */
    public function __construct(
        private readonly array $items = [],
    ) {}

    /**
     * Create a collection from an array.
     *
     * @param  iterable<T>  $items
     */
    public static function make(iterable $items): self
    {
        return new self(is_array($items) ? $items : iterator_to_array($items));
    }

    /**
     * Get all items.
     *
     * @return T[]
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Get an item by key.
     */
    public function get(mixed $key, mixed $default = null): mixed
    {
        return $this->items[$key] ?? $default;
    }

    /**
     * Check if an item exists.
     */
    public function has(mixed $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Check if the collection is empty.
     */
    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    /**
     * Get the first item.
     */
    public function first(?callable $callback = null, mixed $default = null): mixed
    {
        if ($callback === null) {
            return $this->items[0] ?? $default;
        }

        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Get the last item.
     */
    public function last(?callable $callback = null, mixed $default = null): mixed
    {
        if ($callback === null) {
            return $this->items[array_key_last($this->items)] ?? $default;
        }

        $result = $default;

        foreach ($this->items as $key => $value) {
            if ($callback($value, $key)) {
                $result = $value;
            }
        }

        return $result;
    }

    /**
     * Filter items.
     *
     * @return Collection<T>
     */
    public function filter(?callable $callback = null): self
    {
        if ($callback === null) {
            return new self(array_filter($this->items));
        }

        return new self(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * Map items.
     *
     * @template U
     *
     * @param  callable(T, mixed): U  $callback
     * @return Collection<U>
     */
    public function map(callable $callback): self
    {
        return new self(array_map($callback, $this->items, array_keys($this->items)));
    }

    /**
     * Run a callback over each item.
     *
     * @param  callable(T, mixed): void  $callback
     */
    public function each(callable $callback): self
    {
        foreach ($this->items as $key => $value) {
            $callback($value, $key);
        }

        return $this;
    }

    /**
     * Reduce items to a single value.
     *
     * @template U
     *
     * @param  callable(U, T, mixed): U  $callback
     * @param  U  $initial
     * @return U
     */
    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Check if all items pass a test.
     */
    public function every(callable $callback): bool
    {
        foreach ($this->items as $key => $value) {
            if (! $callback($value, $key)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if some items pass a test.
     */
    public function some(callable $callback): bool
    {
        return $this->first($callback) !== null;
    }

    /**
     * Sort items.
     *
     * @return Collection<T>
     */
    public function sort(?callable $callback = null): self
    {
        $items = $this->items;

        if ($callback !== null) {
            usort($items, $callback);
        } else {
            sort($items);
        }

        return new self($items);
    }

    /**
     * Sort items by a key.
     *
     * @return Collection<T>
     */
    public function sortBy(string $key, int $direction = SORT_ASC): self
    {
        $items = $this->items;

        usort($items, function ($a, $b) use ($key, $direction) {
            $aValue = is_array($a) ? ($a[$key] ?? null) : $a->{$key} ?? null;
            $bValue = is_array($b) ? ($b[$key] ?? null) : $b->{$key} ?? null;

            return $direction === SORT_ASC
                ? $aValue <=> $bValue
                : $bValue <=> $aValue;
        });

        return new self($items);
    }

    /**
     * Get unique items.
     *
     * @return Collection<T>
     */
    public function unique(): self
    {
        return new self(array_unique($this->items));
    }

    /**
     * Flatten nested items.
     *
     * @return Collection<T>
     */
    public function flatten(): self
    {
        $result = [];

        array_walk_recursive($this->items, function ($item) use (&$result) {
            $result[] = $item;
        });

        return new self($result);
    }

    /**
     * Chunk items into groups.
     *
     * @return Collection<T[]>
     */
    public function chunk(int $size): self
    {
        return new self(array_chunk($this->items, $size, true));
    }

    /**
     * Get items as a plain array.
     *
     * @return T[]
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Get the number of items.
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator<int, T>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }
}
