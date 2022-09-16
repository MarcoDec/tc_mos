<?php

namespace App\Repository\Purchase\Order;

use App\Entity\Purchase\Order\ProductItem;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ItemRepository<ProductItem>
 *
 * @method null|ProductItem find($id, $lockMode = null, $lockVersion = null)
 * @method null|ProductItem findOneBy(array $criteria, ?array $orderBy = null)
 * @method ProductItem[]    findAll()
 * @method ProductItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ProductItemRepository extends ItemRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ProductItem::class);
    }

    public function createReceiptQueryBuilder(int $id): QueryBuilder {
        return parent::createReceiptQueryBuilder($id)
            ->addSelect('item')
            ->addSelect('item_family')
            ->addSelect('item_references')
            ->leftJoin('i.item', 'item')
            ->leftJoin('item.family', 'item_family')
            ->leftJoin('item.references', 'item_references');
    }

    public function findOneByReceipt(int $id): ?ProductItem {
        $query = $this->createReceiptQueryBuilder($id)->getQuery();
        try {
            /** @phpstan-ignore-next-line */
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }
    }
}
