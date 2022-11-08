<?php

declare(strict_types=1);

namespace App\Repository\Purchase\Component;

use App\Entity\Purchase\Component\Attribute;
use App\Repository\Management\UnitRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Attribute> */
class AttributeRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Attribute::class);
    }

    public function findEager(int $id): ?Attribute {
        /* @phpstan-ignore-next-line */
        return UnitRepository::joinEager(
            qb: $this->createQueryBuilder('a')
                ->where('a.deleted = FALSE')
                ->andWhere('a.id = :id')
                ->setParameter('id', $id),
            join: 'a.unit'
        )
            ->getQuery()
            ->getOneOrNullResult();
    }
}
