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
    
    /**
     * @return Stock[]|null
     */
    public function findStocksByCriteria(): ?array
    {
        $queryBuilder = $this->createQueryBuilder('s');

        // Ajouter les conditions spécifiées
        $queryBuilder
            ->Where('s.jail = :jail')
            ->andWhere('s.quantity.value > 0')
            ->setParameter('jail', 0);

        // Exécuter la requête
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }
}
