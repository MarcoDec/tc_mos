<?php

declare(strict_types=1);

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

class Extension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface {
    /** @param mixed[] $context */
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void {
        $queryBuilder->andWhere("{$queryBuilder->getRootAliases()[0]}.deleted = FALSE");
    }

    /**
     * @param mixed[] $identifiers
     * @param mixed[] $context
     */
    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void {
        $this->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operation, $context);
    }
}
