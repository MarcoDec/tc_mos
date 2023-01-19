<?php

declare(strict_types=1);

namespace App\Repository\Purchase\Component;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use App\Collection;
use App\Doctrine\ORM\QueryBuilder;
use App\Entity\Purchase\Component\Family;
use App\Repository\Provider\ProviderInterface;
use App\Repository\Provider\ProviderTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/** @implements ProviderInterface<Family> */
class FamilyRepository extends NestedTreeRepository implements ProviderInterface {
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
        $em = $this->managerRegistry->getManagerForClass(Family::class);
        parent::__construct($em, $em->getClassMetadata(Family::class));
    }

    private function getOneOrNullItemResult(QueryBuilder $qb): ?Family {
        $root = $qb->getRootAliases()[0];
        /** @var Family[] $families */
        $families = $qb->indexBy($root, "$root.id")->getQuery()->getResult();
        foreach ($qb->getParameters() as $parameter) {
            if (isset($families[$parameter->getValue()])) {
                return $families[$parameter->getValue()];
            }
        }
        return null;
    }

    private function joinItem(QueryBuilder $qb, QueryNameGenerator $generator): QueryBuilder {
        $root = $qb->getRootAliases()[0];
        $depth = $this->createQueryBuilder($root);
        foreach ($qb->getParameters() as $parameter) {
            $depth
                ->innerJoin(
                    join: $this->getClassName(),
                    alias: $parent = $generator->generateJoinAlias('parent'),
                    conditionType: Join::WITH,
                    condition: "$parent.id = :{$parameter->getName()} AND $parent.deleted = FALSE AND $root.root = $parent.root AND $root.lft >= $parent.lft AND $root.rgt <= $parent.rgt"
                )
                ->setParameter($parameter->getName(), $parameter->getValue());
        }
        $attributes = (new Collection($qb->getAllAliases()))->startsWith('attributes_');
        if (empty($attributes)) {
            $attributes = $generator->generateJoinAlias('attributes');
        }
        /* @phpstan-ignore-next-line */
        return $depth
            ->addLeftJoin("$root.attributes", $attributes)
            ->addLeftJoin("$attributes.families", $generator->generateJoinAlias('families'));
    }
}
