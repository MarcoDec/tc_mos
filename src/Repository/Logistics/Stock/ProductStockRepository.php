<?php

namespace App\Repository\Logistics\Stock;

use App\Entity\Logistics\Stock\ProductStock;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends StockRepository<ProductStock>
 */
final class ProductStockRepository extends StockRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ProductStock::class);
    }

    public function getQuantityByProductId(int $productId): ?int
    {
        $qb = $this->createQueryBuilder('ps')
            ->select('SUM(ps.quantity.value) as totalQuantity')
            ->where('ps.item = :productId')
            ->setParameter('productId', $productId);
    
        $result = $qb->getQuery()->getSingleScalarResult();
    
        return $result !== null ? (int)$result : null;
    }
}
