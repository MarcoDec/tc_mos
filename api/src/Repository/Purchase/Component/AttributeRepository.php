<?php

declare(strict_types=1);

namespace App\Repository\Purchase\Component;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryResultItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Doctrine\Orm\State\LinksHandlerTrait;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Metadata\Operation;
use App\Collection;
use App\Doctrine\ORM\QueryBuilder;
use App\Entity\Purchase\Component\Attribute;
use App\Repository\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/** @extends EntityRepository<Attribute> */
class AttributeRepository extends EntityRepository {
    use LinksHandlerTrait;

    /**
     * @param QueryCollectionExtensionInterface[] $collectionExtensions
     * @param QueryItemExtensionInterface[]       $itemExtensions
     */
    public function __construct(
        /** @phpstan-ignore-next-line */
        private readonly ManagerRegistry $managerRegistry,
        private readonly iterable $collectionExtensions,
        private readonly iterable $itemExtensions
    ) {
        parent::__construct($managerRegistry, Attribute::class);
    }

    /**
     * @param  mixed[]                          $uriVariables
     * @param  mixed[]                          $context
     * @return Attribute[]|Paginator<Attribute>
     */
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

    /**
     * @param mixed[]                  $uriVariables
     * @param array{fetch_data?: bool} $context
     */
    public function provideItem(Operation $operation, array $uriVariables = [], array $context = []): ?Attribute {
        $resourceClass = $this->getClassName();
        $fetchData = $context['fetch_data'] ?? true;
        if ($fetchData === false) {
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
        return $this->joinItem($qb, $generator)->getQuery()->getOneOrNullResult();
    }

    private function joinCollection(QueryBuilder $qb, QueryNameGenerator $generator): QueryBuilder {
        return $this
            ->resetDQLJoin($qb)
            ->addLeftJoin(
                join: "{$qb->getRootAliases()[0]}.families",
                alias: $generator->generateJoinAlias('families')
            );
    }

    private function joinItem(QueryBuilder $qb, QueryNameGenerator $generator): QueryBuilder {
        $qb = $this->resetDQLJoin($qb);
        $root = $qb->getRootAliases()[0];
        $unit = (new Collection($qb->getAllAliases()))->startsWith('unit_');
        if (empty($unit)) {
            $qb->addLeftJoin("$root.unit", $unit = $generator->generateJoinAlias('unit'));
        }
        return $qb
            ->addLeftJoin("$root.families", $generator->generateJoinAlias('families'))
            ->addLeftJoin("$unit.attributes", $generator->generateJoinAlias('attributes'));
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
