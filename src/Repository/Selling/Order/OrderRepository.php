<?php

namespace App\Repository\Selling\Order;

use App\Entity\Selling\Order\Order as Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template I of Order
 *
 * @extends ServiceEntityRepository<I>
 *
 * @method I|null find($id, $lockMode = null, $lockVersion = null)
 * @method I|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method I[]    findAll()
 * @method I[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry, string $entityClass = Order::class) {
        parent::__construct($registry, $entityClass);
    }
    public function findById($Id): array {
        return $this->createQueryBuilder('o')
            ->andWhere('o.id = :orderId')
            ->setParameter('orderId', $Id)
            ->getQuery()
            ->getResult();
    }
}