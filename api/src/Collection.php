<?php

declare(strict_types=1);

namespace App;

/** @template T */
class Collection {
    /** @param T[] $items */
    public function __construct(private readonly array $items) {
    }

    /**
     * @param  callable(T): bool $call
     * @return self<T>
     */
    public function filter(callable $call): self {
        return new self(array_filter($this->items, $call));
    }

    /** @return T[] */
    public function toArray(): array {
        return $this->items;
    }
}
