<?php

namespace App\Repository\Logistics\Stock;

use App\Entity\Logistics\Stock\ComponentStock;
use App\Entity\Logistics\Warehouse;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends StockRepository<ComponentStock>
 */
final class ComponentStockRepository extends StockRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ComponentStock::class);
    }

    /**
     * @return ComponentStock[]
     */
    public function findByGrouped(Warehouse $warehouse): array {
        /** @phpstan-ignore-next-line */
        return $this->createGroupedQueryBuilder($warehouse)->getQuery()->getResult();
    }

    protected function createGroupedQueryBuilder(Warehouse $warehouse): QueryBuilder {
        return parent::createGroupedQueryBuilder($warehouse)
            ->addSelect('i')
            ->addSelect('u')
            ->innerJoin('s.item', 'i')
            ->innerJoin('i.unit', 'u');
    }
}
