<?php

namespace App\Repository\Logistics\Stock;

use App\Entity\Logistics\Stock\ComponentStock;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends StockRepository<ComponentStock>
 */
final class ComponentStockRepository extends StockRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ComponentStock::class);
    }
}
