<?php

namespace App\Repository\Selling\Order;

use App\Entity\Selling\Order\ComponentItem;
use App\Entity\Selling\Order\Item;
use App\Entity\Selling\Order\ProductItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

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
class ItemRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry, string $entityClass = Item::class) {
        parent::__construct($registry, $entityClass);
    }

    public function createPatchQueryBuilder(int $id): QueryBuilder {
        return $this->createQueryBuilder('i')->where('i.id = :id')->setParameter('id', $id);
    }

    public function findOneByPatch(int $id): ComponentItem|null|ProductItem {
        return $this->_em->getRepository(ProductItem::class)->findOneByPatch($id)
            ?? $this->_em->getRepository(ComponentItem::class)->findOneByPatch($id);
    }
}
