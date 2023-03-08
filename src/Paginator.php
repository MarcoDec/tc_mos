<?php

namespace App;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @template T of object
 *
 * @implements IteratorAggregate<int, T>
 *
 * @phpstan-ignore-next-line
 */
final class Paginator implements IteratorAggregate, PaginatorInterface {
    /** @param T[] $items */
    public function __construct(
        private readonly array $items,
        private readonly int $itemPerPage,
        private readonly int $page,
        private readonly int $total
    ) {
    }

    public function count(): int {
        return count($this->items);
    }

    public function getCurrentPage(): float {
        return $this->page;
    }

    public function getItemsPerPage(): float {
        return $this->itemPerPage;
    }

    public function getIterator(): Traversable {
        return new ArrayIterator($this->items);
    }

    public function getLastPage(): float {
        return ceil($this->getTotalItems() / $this->getItemsPerPage());
    }

    public function getTotalItems(): float {
        return $this->total;
    }
}
