<?php

declare(strict_types=1);

namespace App\Repository\Purchase\Component;

use App\Entity\Purchase\Component\Attribute;
use App\Repository\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends EntityRepository<Attribute> */
class AttributeRepository extends EntityRepository {
    public function __construct(ManagerRegistry $registry) {
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
}
