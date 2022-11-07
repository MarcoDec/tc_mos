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
     * @param  T            $item
     * @return self<int, T>
     */
    public function push(mixed $item): self {
        return new self([...$this->values(), $item]);
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
