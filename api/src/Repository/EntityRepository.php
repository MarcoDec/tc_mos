<?php

declare(strict_types=1);

namespace App\Repository;

use App\Doctrine\QueryBuilder;
use App\Entity\Entity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template T of Entity
 *
 * @extends ServiceEntityRepository<T>
 */
abstract class EntityRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry, string $entityClass = Entity::class) {
        parent::__construct($registry, $entityClass);
    }

    public function createQueryBuilder($alias, $indexBy = null): QueryBuilder {
        return (new QueryBuilder($this->_em))
            ->select($alias)
            ->from($this->_entityName, $alias, $indexBy)
            ->where("$alias.deleted = FALSE");
    }
}
