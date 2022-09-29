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
}
