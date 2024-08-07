<?php

namespace App\Repository\Manufacturing\Operation;

use App\Collection;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Production\Manufacturing\Operation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


/**
 * @template T of Operation
 *
 * @extends ServiceEntityRepository<T>
 *
 * @method null|T find($id, $lockMode = null, $lockVersion = null)
 * @method null|T findOneBy(array $criteria, ?array $orderBy = null)
 * @method T[]    findAll()
 * @method T[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ManufacturingOperationRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Operation::class);
    }
    public function findByOrderId($orderId): array {
        return $this->createQueryBuilder('o')
            ->andWhere('o.order = :orderId')
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getResult();
    }
}