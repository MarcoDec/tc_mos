<?php

declare(strict_types=1);

namespace App\Repository\Management;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use App\Doctrine\ORM\QueryBuilder;
use App\Entity\Management\Unit;
use App\Repository\Provider\ProviderInterface;
use App\Repository\Provider\ProviderTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/** @implements ProviderInterface<Unit> */
class UnitRepository extends NestedTreeRepository implements ProviderInterface {
    use ProviderTrait;

    /**
     * @param QueryCollectionExtensionInterface[] $collectionExtensions
     * @param QueryItemExtensionInterface[]       $itemExtensions
     */
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly iterable $collectionExtensions,
        private readonly iterable $itemExtensions
    ) {
        /** @var EntityManagerInterface $em */
        $em = $this->managerRegistry->getManagerForClass(Unit::class);
        parent::__construct($em, $em->getClassMetadata(Unit::class));
    }

    private function joinItem(QueryBuilder $qb, QueryNameGenerator $generator): QueryBuilder {
        return $this->resetDQLJoin($qb)
            ->addLeftJoin("{$qb->getRootAliases()[0]}.attributes", $generator->generateJoinAlias('attributes'));
    }
}
