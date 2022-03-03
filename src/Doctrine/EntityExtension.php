<?php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Entity;
use Doctrine\ORM\QueryBuilder;

final class EntityExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface {
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null): void {
        if (is_subclass_of($resourceClass, Entity::class)) {
            $queryBuilder->andWhere("{$queryBuilder->getRootAliases()[0]}.deleted = FALSE");
        }
    }

    /**
     * @param int[]|string[] $identifiers
     * @param mixed[]        $context
     */
    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?string $operationName = null, array $context = []): void {
        $this->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName);
    }
}
