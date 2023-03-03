<?php

namespace App;

use ArrayIterator;
use ArrayObject;
use IteratorAggregate;

/**
 * @template K of int|string
 * @template T
 *
 * @implements IteratorAggregate<K, T>
 */
final class Collection implements IteratorAggregate {
    /** @param array<K, T> $items */
    public function __construct(private array $items = []) {
    }

    /**
     * @template P of int|string
     * @template I
     *
     * @param array<P, I> $items
     *
     * @return self<P, I>
     */
    public static function collect(array $items): self {
        return new self($items);
    }

    /** @return array<K, T> */
    public function all(): array {
        return $this->items;
    }

    /** @param callable(T): bool $callable */
    public function contains(callable $callable): bool {
        foreach ($this->items as $item) {
            if ($callable($item)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param callable(T, K): bool $callable
     *
     * @return self<K, T>
     */
    public function filter(callable $callable): self {
        return new self(array_filter($this->items, $callable, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * @param callable(T): bool $callable
     *
     * @return null|T
     */
    public function first(callable $callable): mixed {
        foreach ($this->items as $item) {
            if ($callable($item)) {
                return $item;
            }
        }
        return null;
    }

    /** @return self<int|string, mixed> */
    public function flatten(?int $depth = null, bool $preserveKeys = false): self {
        $items = [];
        $put = static function (int|null|string $key, mixed $value) use (&$items, $preserveKeys): void {
            if ($preserveKeys) {
                $items[$key] = $value;
            } else {
                $items[] = $value;
            }
        };
        array_walk($this->items, static function (mixed $item, int|null|string $key) use ($depth, $preserveKeys, $put): void {
            if ($depth === null || $depth > 0) {
                if (is_array($item)) {
                    $item = new self($item);
                }
                if ($item instanceof self) {
                    foreach ($item->flatten($depth === null ? $depth : $depth - 1, $preserveKeys) as $i => $value) {
                        $put($i, $value);
                    }
                    return;
                }
            }
            $put($key, $item);
        });
        return new self($items);
    }

    /**
     * @param K $key
     *
     * @return null|T
     */
    public function get(int|string $key): mixed {
        return $this->items[$key] ?? null;
    }

    /** @return ArrayIterator<K, T> */
    public function getIterator(): ArrayIterator {
        return new ArrayIterator($this->items);
    }

    /** @param K $key */
    public function has(int|string $key): bool {
        return isset($this->items[$key]);
    }

    public function implode(string $separator): string {
        return implode($separator, $this->items);
    }

    /** @return self<int, K> */
    public function keys(): self {
        return new self(array_keys($this->items));
    }

    /**
     * @template I
     *
     * @param callable(T, K): I $callable
     *
     * @return self<K, I>
     */
    public function map(callable $callable): self {
        return new self(array_combine($keys = $this->keys()->all(), array_map($callable, $this->items, $keys)));
    }

    /**
     * @template P of int|string
     * @template I
     *
     * @param callable(T): array<P, I> $callable
     *
     * @return self<P, I>
     */
    public function mapWithKeys(callable $callable): self {
        $items = [];
        foreach ($this->items as $item) {
            foreach ($callable($item) as $key => $value) {
                $items[$key] = $value;
            }
        }
        return new self($items);
    }

    /**
     * @template P of int|string
     * @template I
     *
     * @param array<P, I>|self<P, I> $items
     *
     * @return self<int, I|T>
     */
    public function merge(array|self $items): self {
        return new self(array_values(array_merge($this->items, $items instanceof self ? $items->all() : $items)));
    }

    /**
     * @param T $item
     *
     * @return $this
     */
    public function prepend(mixed $item): self {
        array_unshift($this->items, $item);
        return $this;
    }

    /**
     * @param T $item
     *
     * @return $this
     */
    public function push(mixed $item): self {
        /** @phpstan-ignore-next-line */
        $this->items[] = $item;
        return $this;
    }

    /**
     * @param K $key
     * @param T $item
     *
     * @return $this
     */
    public function put(int|string $key, mixed $item): self {
        $this->items[$key] = $item;
        return $this;
    }

    /**
     * @return self<K, T>
     */
    public function sort(): self {
        $items = (new ArrayObject($this->items))->getArrayCopy();
        sort($items);
        return new self($items);
    }

    /**
     * @param (callable(T): mixed)|int|string $sorter
     *
     * @return self<K, T>
     */
    public function sortBy(callable|int|string $sorter): self {
        $items = (new ArrayObject($this->items))->getArrayCopy();
        usort($items, static function (mixed $a, mixed $b) use ($sorter): int {
            $vA = is_callable($sorter) ? $sorter($a) : $a[$sorter];
            $vB = is_callable($sorter) ? $sorter($b) : $b[$sorter];
            if (is_numeric($vA) && is_numeric($vB)) {
                return ((float) $vA < (float) $vB) ? -1 : 0;
            }
            if (is_string($vA) && is_string($vB)) {
                return strcmp($vA, $vB);
            }
            return 0;
        });
        return new self($items);
    }

    /** @return self<K, T> */
    public function sortKeys(): self {
        $items = (new ArrayObject($this->items))->getArrayCopy();
        ksort($items);
        return new self($items);
    }

    /**
     * @param callable(T): mixed $callable
     *
     * @return self<int, T>
     */
    public function unique(callable $callable): self {
        $items = [];
        $uniques = [];
        foreach ($this->items as $item) {
            if (!in_array($id = $callable($item), $uniques)) {
                $items[] = $item;
                $uniques[] = $id;
            }
        }
        return new self($items);
    }
}
