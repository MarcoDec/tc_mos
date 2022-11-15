<?php

declare(strict_types=1);

namespace App\Repository\Purchase\Component;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use App\Collection;
use App\Doctrine\ORM\QueryBuilder;
use App\Entity\Purchase\Component\Attribute;
use App\Repository\Provider\ProviderInterface;
use App\Repository\Provider\ProviderTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Attribute>
 * @implements ProviderInterface<Attribute>
 */
class AttributeRepository extends ServiceEntityRepository implements ProviderInterface {
    use ProviderTrait;

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
}
