<?php

namespace App\CouchDB\Repository\Finder;

use JetBrains\PhpStorm\Pure;

final class Finder {
    private Selector $selector;

    /**
     * @param string[] $fields
     * @param array<string, string>[] $sort
     */
    #[Pure]
    public function __construct(
        private array $fields = [],
        private int $limit = 25,
        ?Selector $selector = null,
        private array $sort = [['id' => 'asc']]
    ) {
        $this->selector = $selector ?? new Selector();
    }

    /**
     * @return string[]
     */
    public function getFields(): array {
        return $this->fields;
    }

    public function getLimit(): int {
        return $this->limit;
    }

    public function getSelector(): Selector {
        return $this->selector;
    }

    /**
     * @return array<string, string>[]
     */
    public function getSort(): array {
        return $this->sort;
    }

    public function where(string $field, mixed $value): self {
        $this->selector->where($field, $value);
        return $this;
    }
}
