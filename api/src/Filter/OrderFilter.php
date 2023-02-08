<?php

declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter as OrmOrderFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryBuilderHelper;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Exception\InvalidArgumentException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

class OrderFilter extends OrmOrderFilter {
    /** @return array{string, string, string[]} */
    protected function addJoinsForNestedProperty(string $property, string $rootAlias, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $joinType): array {
        $propertyParts = $this->splitPropertyParts($property, $resourceClass);
        $parentAlias = $rootAlias;
        $alias = null;

        foreach ($propertyParts['associations'] as $association) {
            $alias = QueryBuilderHelper::addJoinOnce(
                queryBuilder: $queryBuilder,
                queryNameGenerator: $queryNameGenerator,
                alias: $parentAlias,
                association: $association,
                joinType: $joinType,
                conditionType: Join::WITH,
                condition: "$parentAlias.deleted = FALSE"
            );
            $parentAlias = $alias;
        }

        if (null === $alias) {
            throw new InvalidArgumentException(sprintf('Cannot add joins for property "%s" - property is not nested.', $property));
        }

        return [$alias, $propertyParts['field'], $propertyParts['associations']];
    }
}
