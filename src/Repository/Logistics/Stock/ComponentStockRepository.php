<?php

namespace App\Repository\Logistics\Stock;

use App\Entity\Logistics\Stock\ComponentStock;
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
    public function findFetched(array $filters): array {
        /** @phpstan-ignore-next-line */
        return $this->createFetchedQueryBuilder($filters)->getQuery()->getResult();
    }

    protected function createFetchedQueryBuilder(array $filters): QueryBuilder {
        return parent::createFetchedQueryBuilder($filters)
            ->addSelect('f')
            ->addSelect('i')
            ->addSelect('u')
            ->innerJoin('s.item', 'i')
            ->innerJoin('i.family', 'f')
            ->innerJoin('i.unit', 'u');
    }
}
