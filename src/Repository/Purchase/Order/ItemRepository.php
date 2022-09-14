<?php

namespace App\Repository\Purchase\Order;

use App\Entity\Purchase\Order\ComponentItem;
use App\Entity\Purchase\Order\Item;
use App\Entity\Purchase\Order\ProductItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template I of Item
 *
 * @extends ServiceEntityRepository<I>
 *
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry, string $entityClass = Item::class) {
        parent::__construct($registry, $entityClass);
    }

    public function createReceiptQueryBuilder(int $id): QueryBuilder {
        return $this->createQueryBuilder('i')
            ->addSelect('c')
            ->addSelect('company')
            ->addSelect('company_references')
            ->addSelect('o')
            ->addSelect('r')
            ->addSelect('ref')
            ->addSelect('s')
            ->addSelect('s_references')
            ->leftJoin('i.order', 'o')
            ->leftJoin('o.company', 'company')
            ->leftJoin('company.references', 'company_references')
            ->leftJoin('o.supplier', 's')
            ->leftJoin('s.references', 's_references')
            ->leftJoin('i.receipts', 'r')
            ->leftJoin('r.checks', 'c')
            ->leftJoin('c.reference', 'ref')
            ->where('i.id = :id')
            ->setParameter('id', $id);
    }

    public function findOneByReceipt(int $id): ComponentItem|ProductItem|null {
        return $this->_em->getRepository(ComponentItem::class)->findOneByReceipt($id)
            ?? $this->_em->getRepository(ProductItem::class)->findOneByReceipt($id);
    }
}
