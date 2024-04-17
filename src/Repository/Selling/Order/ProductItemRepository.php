<?php

namespace App\Repository\Selling\Order;

use App\Filter\RelationFilter;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Expr\Join;
use App\Doctrine\DBAL\Types\ItemType;
use App\Entity\Selling\Order\ProductItem;
use Doctrine\Persistence\ManagerRegistry;
use ApiPlatform\Core\Annotation\ApiFilter;

use Doctrine\ORM\NonUniqueResultException;
use App\Entity\Selling\Order\ComponentItem;

/**
 * @extends ItemRepository<ProductItem>
 *
 * @method null|ProductItem find($id, $lockMode = null, $lockVersion = null)
 * @method null|ProductItem findOneBy(array $criteria, ?array $orderBy = null)
 * @method ProductItem[]    findAll()
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

    public function createByQueryBuilder(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): QueryBuilder {
        return parent::createByQueryBuilder($criteria, $orderBy, $limit, $offset)
            ->addSelect('item')
            ->addSelect('item_family')
            ->addSelect('u')
            ->leftJoin('i.item', 'item', Join::WITH, 'item.deleted = FALSE')
            ->leftJoin('item.family', 'item_family', Join::WITH, 'item_family.deleted = FALSE')
            ->leftJoin('item.unit', 'u', Join::WITH, 'u.deleted = FALSE');
    }

    public function createCheckQueryBuilder(int $id): QueryBuilder {
        return parent::createCheckQueryBuilder($id)
            ->addSelect('family_references')
            ->addSelect('item')
            ->addSelect('item_family')
            ->addSelect('item_references')
            ->addSelect('u')
            ->leftJoin('i.item', 'item', Join::WITH, 'item.deleted = FALSE')
            ->leftJoin('item.family', 'item_family', Join::WITH, 'item_family.deleted = FALSE')
            ->leftJoin('item_family.references', 'family_references', Join::WITH, 'family_references.deleted = FALSE')
            ->leftJoin('item.references', 'item_references', Join::WITH, 'item_references.deleted = FALSE')
            ->leftJoin('item.unit', 'u', Join::WITH, 'u.deleted = FALSE');
    }

    public function createReceiptQueryBuilder(int $id): QueryBuilder {
        return parent::createReceiptQueryBuilder($id)
            ->addSelect('item')
            ->addSelect('u')
            ->leftJoin('i.item', 'item', Join::WITH, 'item.deleted = FALSE')
            ->leftJoin('item.unit', 'u', Join::WITH, 'u.deleted = FALSE');
    }

    /**
     * @return ComponentItem[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        /** @phpstan-ignore-next-line */
        return $this->createByQueryBuilder($criteria, $orderBy, $limit, $offset)->getQuery()->getResult();
    }

    public function findOneByCheck(int $id): ?ComponentItem {
        $query = $this->createCheckQueryBuilder($id)->getQuery();
        try {
            /** @phpstan-ignore-next-line */
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }
    }

    public function findOneByReceipt(int $id, string $ressourceClass): ?ComponentItem {
        $query = $this->createReceiptQueryBuilder($id)->getQuery();
        try {
            /** @phpstan-ignore-next-line */
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }
    }
    public function findByProductId(int $productId): array {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.item', 'p')
            ->andWhere('p.id = :productId')
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getResult();
    }
    
}
