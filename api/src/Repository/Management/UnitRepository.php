<?php

declare(strict_types=1);

namespace App\Repository\Management;

use App\Entity\Management\Unit;
use App\Repository\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends EntityRepository<Unit> */
class UnitRepository extends EntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Unit::class);
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?Unit {
        return $this->findOneBy(['deleted' => false, 'id' => $id]);
    }

    public function findEager(int $id): ?Unit {
        /* @phpstan-ignore-next-line */
        return $this->createQueryBuilder('u')
            ->addLeftJoin('u.attributes', 'a')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPatch(int $id): ?Unit {
        /* @phpstan-ignore-next-line */
        return $this->createQueryBuilder('u')
            ->addLeftJoin('u.children', 'c')
            ->addLeftJoin('u.parent', 'p')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
