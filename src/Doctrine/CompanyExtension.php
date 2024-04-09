<?php

namespace App\Doctrine;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Interfaces\CompanyInterface;
use App\Security\SecurityTrait;
use Doctrine\ORM\QueryBuilder;

final class CompanyExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface {
    use SecurityTrait;

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?string $operationName = null): void {
        if (is_subclass_of($resourceClass, CompanyInterface::class)) {
            $field = 'company';
            $param = $queryNameGenerator->generateParameterName($field);
            $queryBuilder
                ->andWhere("{$queryBuilder->getRootAliases()[0]}.$field = :$param")
                ->setParameter($param, $this->getCompanyId());
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
