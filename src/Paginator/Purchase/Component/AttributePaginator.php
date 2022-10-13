<?php

namespace App\Paginator\Purchase\Component;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use App\Entity\Purchase\Component\Attribute;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, Attribute>
 *
 * @phpstan-ignore-next-line
 */
final class AttributePaginator implements IteratorAggregate, PaginatorInterface {
    /**
     * @param Attribute[] $attributes
     */
    public function __construct(
        private readonly array $attributes,
        private readonly int $itemPerPage,
        private readonly int $page,
        private readonly int $total
    ) {
    }

    public function count(): int {
        return count($this->attributes);
    }

    public function getCurrentPage(): float {
        return $this->page;
    }

    public function getItemsPerPage(): float {
        return $this->itemPerPage;
    }

    public function getIterator(): Traversable {
        return new ArrayIterator($this->attributes);
    }

    public function getLastPage(): float {
        return ceil($this->getTotalItems() / $this->getItemsPerPage());
    }

    public function getTotalItems(): float {
        return $this->total;
    }
}
