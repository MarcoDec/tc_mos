<?php

namespace App\Repository\Selling\Order;

use App\Entity\Selling\Order\ProductItem;
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

    public function createPatchQueryBuilder(int $id): QueryBuilder {
        return parent::createPatchQueryBuilder($id)->addSelect('p')->innerJoin('i.item', 'p');
    }

    public function findOneByPatch(int $id): ?ProductItem {
        $query = $this->createPatchQueryBuilder($id)->getQuery();
        try {
            /** @phpstan-ignore-next-line */
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
