<?php

namespace App\Repository\Logistics\Stock;

use App\Entity\Logistics\Stock\ProductStock;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends StockRepository<ProductStock>
 */
final class ProductStockRepository extends StockRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ProductStock::class);
    }

    /**
     * @return ProductStock[]
     */
    public function findFetched(array $filters): array {
        /** @phpstan-ignore-next-line */
        return $this->createFetchedQueryBuilder($filters)->getQuery()->getResult();
    }

    protected function createFetchedQueryBuilder(array $filters): QueryBuilder {
        return parent::createFetchedQueryBuilder($filters)
            ->addSelect('i')
            ->addSelect('u')
            ->innerJoin('s.item', 'i')
            ->innerJoin('i.unit', 'u');
    }
}
