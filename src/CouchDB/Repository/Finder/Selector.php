<?php

namespace App\CouchDB\Repository\Finder;

final class Selector {
    /**
     * @param array<string, mixed> $selector
     */
    public function __construct(private array $selector = []) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getSelector(): array {
        return $this->selector;
    }

    public function greaterThan(string $field, int $min = 0): self {
        $this->selector[$field] = ['$gt' => $min];
        return $this;
    }

    public function where(string $field, mixed $value): self {
        $this->selector[$field] = $value;
        return $this;
    }
}
