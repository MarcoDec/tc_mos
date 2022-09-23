<?php

namespace App\Repository\Purchase\Order;

use App\Entity\Purchase\Order\ComponentItem;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ItemRepository<ComponentItem>
 *
 * @method ComponentItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComponentItem|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method ComponentItem[]    findAll()
 */
final class ComponentItemRepository extends ItemRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ComponentItem::class);
    }

    public function createByQueryBuilder(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): QueryBuilder {
        return parent::createByQueryBuilder($criteria, $orderBy, $limit, $offset)
            ->addSelect('item')
            ->addSelect('item_family')
            ->leftJoin('i.item', 'item')
            ->leftJoin('item.family', 'item_family');
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

    /**
     * @return ComponentItem[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        /** @phpstan-ignore-next-line */
        return $this->createByQueryBuilder($criteria, $orderBy, $limit, $offset)->getQuery()->getResult();
    }

    public function findOneByReceipt(int $id): ?ComponentItem {
        $query = $this->createReceiptQueryBuilder($id)->getQuery();
        try {
            /** @phpstan-ignore-next-line */
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }
    }
}
