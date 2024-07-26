<?php

namespace App\Repository\Manufacturing\Product;

use App\Entity\Production\Manufacturing\Order;
use App\Entity\Purchase\Order\ProductItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;;
use App\Repository\Purchase\Order\ItemRepository;

/**
 * @template I of Item
 *
 * @extends ServiceEntityRepository<I>
 *
 * @method I|null find($id, $lockMode = null, $lockVersion = null)
 * @method I|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method I[]    findAll()
 * @method I[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ManufacturingProductItemRepository  extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry, string $entityClass = Order::class) {
        parent::__construct($registry, $entityClass);
    }

    public function findByEmbBlockerAndEmbState(): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.embBlocker.state = :enabled')
            ->andWhere('i.embState.state IN (:states)')
            ->setParameters([
                'enabled' => 'enabled',
                'states' => ['agreed', 'partially_dellivered'],
            ])
            ->getQuery()
            ->getResult();
    }
}