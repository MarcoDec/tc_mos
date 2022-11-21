<?php

declare(strict_types=1);

namespace App\Repository\Management\Unit;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use App\Doctrine\ORM\QueryBuilder;
use App\Entity\Management\Unit\Unit;
use App\Repository\Provider\ProviderInterface;
use App\Repository\Provider\ProviderTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * @template T of Unit
 *
 * @implements ProviderInterface<T>
 */
class UnitRepository extends NestedTreeRepository implements ProviderInterface {
    use ProviderTrait;

    /**
     * @param QueryCollectionExtensionInterface[] $collectionExtensions
     * @param QueryItemExtensionInterface[]       $itemExtensions
     * @param class-string<T>                     $entityClass
     */
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly iterable $collectionExtensions,
        private readonly iterable $itemExtensions,
        string $entityClass = Unit::class
    ) {
        /** @var EntityManagerInterface $em */
        $em = $this->managerRegistry->getManagerForClass($entityClass);
        parent::__construct($em, $em->getClassMetadata($entityClass));
    }

    public function createQueryBuilder($alias, $indexBy = null): QueryBuilder {
        return (new QueryBuilder($this->_em))
            ->select($alias)
            ->from($this->_entityName, $alias, $indexBy);
    }

    private function joinItem(QueryBuilder $qb, QueryNameGenerator $generator): QueryBuilder {
        return $this->resetDQLJoin($qb)
            ->addLeftJoin("{$qb->getRootAliases()[0]}.attributes", $generator->generateJoinAlias('attributes'));
    }
}
