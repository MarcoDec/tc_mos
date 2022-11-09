<?php

declare(strict_types=1);

namespace App\Repository;

use App\Doctrine\ORM\QueryBuilderTrait;
use App\Entity\Entity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template T of Entity
 *
 * @extends ServiceEntityRepository<T>
 */
abstract class EntityRepository extends ServiceEntityRepository {
    use QueryBuilderTrait;

    public function __construct(ManagerRegistry $registry, string $entityClass = Entity::class) {
        parent::__construct($registry, $entityClass);
    }
}
