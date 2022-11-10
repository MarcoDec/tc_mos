<?php

declare(strict_types=1);

namespace App\Repository\Purchase\Component;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Doctrine\Orm\State\LinksHandlerTrait;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Metadata\Operation;
use App\Doctrine\ORM\QueryBuilder;
use App\Entity\Purchase\Component\Attribute;
use App\Repository\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends EntityRepository<Attribute> */
class AttributeRepository extends EntityRepository {
    use LinksHandlerTrait;

    /** @param QueryCollectionExtensionInterface[] $collectionExtensions */
    public function __construct(ManagerRegistry $registry, private readonly iterable $collectionExtensions) {
        parent::__construct($registry, Attribute::class);
    }

    public function findEager(int $id): ?Attribute {
        /* @phpstan-ignore-next-line */
        return $this->createQueryBuilder('a')
            ->addLeftJoin('a.unit', 'u')
            ->addLeftJoin('u.attributes', 'u_a')
            ->andWhere('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param  mixed[]                          $uriVariables
     * @param  mixed[]                          $context
     * @return Attribute[]|Paginator<Attribute>
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|Paginator {
        $qb = $this->createQueryBuilder('a');
        $generator = new QueryNameGenerator();
        $resourceClass = $this->getClassName();
        $this->handleLinks($qb, $uriVariables, $generator, $context, $resourceClass, $operation);
        foreach ($this->collectionExtensions as $extension) {
            $extension->applyToCollection($qb, $generator, $resourceClass, $operation, $context);
            if ($extension instanceof QueryResultCollectionExtensionInterface && $extension->supportsResult($resourceClass, $operation, $context)) {
                /* @phpstan-ignore-next-line */
                return $extension->getResult($this->join($qb, $generator), $resourceClass, $operation, $context);
            }
        }
        /* @phpstan-ignore-next-line */
        return $this->join($qb, $generator)->getQuery()->getResult();
    }

    private function join(QueryBuilder $qb, QueryNameGenerator $generator): QueryBuilder {
        return $qb->addLeftJoin(
            join: "{$qb->getRootAliases()[0]}.families",
            alias: $generator->generateJoinAlias('families')
        );
    }
}
