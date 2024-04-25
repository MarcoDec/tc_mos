<?php

namespace App\Repository\Manufacturing\Order;

use App\Collection;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Production\Manufacturing\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


/**
 * @template T of Order
 *
 * @extends ServiceEntityRepository<T>
 *
 * @method null|T find($id, $lockMode = null, $lockVersion = null)
 * @method null|T findOneBy(array $criteria, ?array $orderBy = null)
 * @method T[]    findAll()
 * @method T[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ManufacturingOrderRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Order::class);
    }

}