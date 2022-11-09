<?php

declare(strict_types=1);

namespace App\Repository\Management;

use App\Doctrine\ORM\QueryBuilderTrait;
use App\Entity\Management\Unit;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class UnitRepository extends NestedTreeRepository {
    use QueryBuilderTrait;

    public function findEager(int $id): ?Unit {
        /* @phpstan-ignore-next-line */
        return $this->createQueryBuilder('u')
            ->addLeftJoin('u.attributes', 'a')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
