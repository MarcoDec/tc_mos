<?php

declare(strict_types=1);

namespace App;

/**
 * @template K of int|string
 * @template T
 */
class Collection {
    /** @var array<int, K> */
    private const EMPTY = [];

    /** @param array<K, T> $items */
    public function __construct(private readonly array $items) {
    }

    /**
     * @param  non-empty-string  $separator
     * @return self<int, string>
     */
    public static function explode(string $separator, string $array): self {
        return new self(explode($separator, $array));
    }

    /** @param T $item */
    public function contains(mixed $item): bool {
        return in_array($item, $this->items, true);
    }

    public function count(): int {
        return count($this->items);
    }

    /**
     * @param  callable(T, K): bool $call
     * @return self<K, T>
     */
    public function filter(callable $call): self {
        return new self(array_filter($this->items, $call, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * @param  callable(T): bool $call
     * @return null|T
     */
    public function find(callable $call): mixed {
        foreach ($this->items as $item) {
            if ($call($item)) {
                return $item;
            }
        }
        return null;
    }

    /**
     * @param  K      $key
     * @return null|T
     */
    public function get(string|int $key): mixed {
        return $this->items[$key] ?? null;
    }

    public function implode(string $separator): string {
        return implode($separator, $this->items);
    }

    /** @return self<int, K> */
    public function keys(): self {
        return new self(array_keys($this->items));
    }

    /**
     * @template U
     * @param  callable(T, K): U $call
     * @return self<K, U>
     */
    public function map(callable $call): self {
        return new self(array_combine($keys = $this->keys()->toArray(), array_map($call, $this->items, $keys)));
    }

    /**
     * @template L of int|string
     * @param  callable(K, T): L $call
     * @return self<L, T>
     */
    public function mapKeys(callable $call): self {
        return new self(array_combine(array_map($call, $this->keys()->toArray(), $this->items), $this->items));
    }

    /**
     * @template L of int|string
     * @template U
     * @param  callable(T): array<L, U> $call
     * @return self<L, U>
     */
    public function mapWithKeys(callable $call): self {
        $items = [];
        foreach ($this->items as $item) {
            foreach ($call($item) as $key => $value) {
                $items[$key] = $value;
            }
        }
        return new self($items);
    }

    /** @return self<int, K> */
    public function maxKeys(): self {
        $keys = new self(self::EMPTY);
        foreach ($this->items as $item) {
            if (is_array($item)) {
                $item = new self($item);
            }
            if ($item instanceof self && $item->count() > $keys->count()) {
                $keys = $item->keys();
            }
        }
        return $keys;
    }

    /**
     * @template L of int|string
     * @template U
     * @param  array<L, U>    $items
     * @return self<K|L, T|U>
     */
    public function merge(array $items): self {
        return new self(array_merge($this->items, $items));
    }

    /**
     * @param  T            $item
     * @return self<int, T>
     */
    public function push(mixed $item): self {
        return new self([...$this->values(), $item]);
    }

    /**
     * @param  T          $removed
     * @return self<K, T>
     */
    public function remove(mixed $removed): self {
        return $this->filter(static fn (mixed $item): bool => $item !== $removed);
    }

    /**
     * @param  callable(T, T): int $call
     * @return self<K, T>
     */
    public function sortBy(callable $call): self {
        $items = $this->items;
        usort($items, $call);
        return new self($items);
    }

    /** @return self<K, T> */
    public function sortKeys(): self {
        $items = $this->items;
        ksort($items);
        return new self($items);
    }

    public function startsWith(string $start): ?string {
        /* @phpstan-ignore-next-line */
        return $this->find(static fn (string $item): bool => str_starts_with($item, $start));
    }

    /** @return array<K, T> */
    public function toArray(): array {
        return $this->items;
    }

    /** @return array<int, T> */
    public function values(): array {
        return array_values($this->items);
    }
}
