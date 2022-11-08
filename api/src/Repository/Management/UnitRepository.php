<?php

declare(strict_types=1);

namespace App\Repository\Management;

use App\Entity\Management\Unit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Unit> */
class UnitRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Unit::class);
    }

    public static function joinEager(QueryBuilder $qb, string $join, string $alias = 'u'): QueryBuilder {
        return self::eager(
            qb: $qb
                ->addSelect($alias)
                ->leftJoin($join, $alias, Join::WITH, "$alias.deleted = FALSE"),
            alias: $alias
        );
    }

    private static function eager(QueryBuilder $qb, string $alias = 'u'): QueryBuilder {
        return $qb
            ->addSelect($attrs = "{$alias}_a")
            ->leftJoin("$alias.attributes", $attrs, Join::WITH, "$attrs.deleted = FALSE");
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?Unit {
        return $this->findOneBy(['deleted' => false, 'id' => $id]);
    }

    public function findEager(int $id): ?Unit {
        /* @phpstan-ignore-next-line */
        return self::eager(
            $this->createQueryBuilder('u')
                ->where('u.deleted = FALSE')
                ->andWhere('u.id = :id')
                ->setParameter('id', $id)
        )
            ->getQuery()
            ->getOneOrNullResult();
    }
}
