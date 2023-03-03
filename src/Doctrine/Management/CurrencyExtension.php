<?php

namespace App\Doctrine\Management;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Management\Currency;
use Doctrine\ORM\QueryBuilder;

final class CurrencyExtension implements QueryCollectionExtensionInterface {
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null): void {
        if ($resourceClass === Currency::class && $operationName === 'options') {
            $queryBuilder->andWhere("{$queryBuilder->getRootAliases()[0]}.active = TRUE");
        }
    }
}
