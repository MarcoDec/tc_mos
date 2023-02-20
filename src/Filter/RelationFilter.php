<?php

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Collection;

final class RelationFilter extends SearchFilter {
    /**
     * @return array<string, array<string, mixed>>
     */
    public function getDescription(string $resourceClass): array {
        return Collection::collect(parent::getDescription($resourceClass))
            ->filter(static fn (array $value, string $key): bool => !str_ends_with($key, '[]'))
            ->all();
    }
}
