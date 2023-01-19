<?php

declare(strict_types=1);

namespace App\Repository\Provider;

use ApiPlatform\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryResultItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Doctrine\Orm\State\LinksHandlerTrait;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Metadata\Operation;
use App\Doctrine\ORM\QueryBuilder;
use App\Doctrine\ORM\QueryBuilderTrait;
use Doctrine\ORM\Query\Expr\Join;

trait ProviderTrait {
    use LinksHandlerTrait;
    use QueryBuilderTrait;

    public function provideCollection(Operation $operation, array $uriVariables = [], array $context = []): array|Paginator {
        $resourceClass = $this->getClassName();
        $qb = $this->createQueryBuilder('a');
        $generator = new QueryNameGenerator();
        $this->handleLinks($qb, $uriVariables, $generator, $context, $resourceClass, $operation);
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($qb, $generator, $resourceClass, $operation, $context);
            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operation, $context)) {
                /* @phpstan-ignore-next-line */
                return $extension->getResult($this->joinCollection($qb, $generator), $resourceClass, $operation, $context);
            }
        }
        /* @phpstan-ignore-next-line */
        return $this->joinCollection($qb, $generator)->getQuery()->getResult();
    }

    public function provideItem(Operation $operation, array $uriVariables = [], array $context = []): mixed {
        $resourceClass = $this->getClassName();
        $fetchData = $context['fetch_data'] ?? true;
        if ($fetchData === false) {
            /* @phpstan-ignore-next-line */
            return $this->_em->getReference($resourceClass, $uriVariables);
        }
        $qb = $this->createQueryBuilder('a');
        $generator = new QueryNameGenerator();
        $this->handleLinks($qb, $uriVariables, $generator, $context, $resourceClass, $operation);
        foreach ($this->itemExtensions as $extension) {
            $extension->applyToItem($qb, $generator, $resourceClass, $uriVariables, $operation, $context);
            if ($extension instanceof QueryResultItemExtensionInterface && $extension->supportsResult($resourceClass, $operation, $context)) {
                /* @phpstan-ignore-next-line */
                return $extension->getResult($this->joinItem($qb, $generator), $resourceClass, $operation, $context);
            }
        }
        /* @phpstan-ignore-next-line */
        return $this->getOneOrNullItemResult($this->joinItem($qb, $generator));
    }

    private function getOneOrNullItemResult(QueryBuilder $qb): mixed {
        return $qb->getQuery()->getOneOrNullResult();
    }

    private function joinCollection(QueryBuilder $qb, QueryNameGenerator $generator): QueryBuilder {
        return $this->resetDQLJoin($qb);
    }

    private function joinItem(QueryBuilder $qb, QueryNameGenerator $generator): QueryBuilder {
        return $this->resetDQLJoin($qb);
    }

    private function resetDQLJoin(QueryBuilder $qb): QueryBuilder {
        /** @var array<string, Join[]> $parts */
        $parts = $qb->getDQLPart('join');
        $qb->resetDQLPart('join');
        foreach ($parts as $alias => $part) {
            foreach ($part as $expression) {
                $qb->addLeftJoin($expression->getJoin(), $expression->getAlias() ?? $alias);
            }
        }
        return $qb;
    }
}
