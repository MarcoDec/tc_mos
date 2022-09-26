<?php

namespace App\Repository\Logistics\Stock;

use App\Entity\Logistics\Stock\ComponentStock;
use App\Entity\Logistics\Warehouse;
use Doctrine\ORM\Query\Expr\Join;
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
            ->addSelect('partial f.{code, id}')
            ->addSelect('partial i.{id}')
            ->addSelect('u')
            ->innerJoin('s.item', 'i', Join::WITH, 'i.deleted = FALSE')
            ->innerJoin('i.family', 'f', Join::WITH, 'f.deleted = FALSE')
            ->innerJoin('i.unit', 'u', Join::WITH, 'u.deleted = FALSE');
    }
}
