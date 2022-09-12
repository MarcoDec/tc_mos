<?php

namespace App\Repository\Logistics\Stock;

use App\Entity\Logistics\Stock\ComponentStock;
use App\Entity\Logistics\Stock\ProductStock;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Logistics\Warehouse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template T of Stock
 *
 * @extends ServiceEntityRepository<T>
 *
 * @method null|T find($id, $lockMode = null, $lockVersion = null)
 * @method null|T findOneBy(array $criteria, ?array $orderBy = null)
 * @method T[]    findAll()
 * @method T[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry, string $entityClass = Stock::class) {
        parent::__construct($registry, $entityClass);
    }

    /**
     * @return (ComponentStock|ProductStock)[]
     */
    public function findByGrouped(Warehouse $warehouse): array {
        /** @var (ComponentStock|ProductStock)[] $componentsStocks */
        $componentsStocks = $this->_em->getRepository(ComponentStock::class)->findByGrouped($warehouse);
        /** @var (ComponentStock|ProductStock)[] $productStocks */
        $productStocks = $this->_em->getRepository(ProductStock::class)->findByGrouped($warehouse);
        return collect($componentsStocks)->merge($productStocks)->values()->all();
    }

    /**
     * @return null|T
     */
    public function findTransfer(int $id): ?Stock {
        /** @phpstan-ignore-next-line */
        return $this->_em->getRepository(ComponentStock::class)->findTransferStock($id)
            ?? $this->_em->getRepository(ProductStock::class)->findTransferStock($id);
    }

    protected function createGroupedQueryBuilder(Warehouse $warehouse): QueryBuilder {
        return $this->createQueryBuilder('s')
            ->where('s.warehouse = :warehouse')
            ->setParameter('warehouse', $warehouse->getId());
    }

    /**
     * @return null|T
     */
    protected function findTransferStock(int $id): ?Stock {
        $query = $this
            ->createQueryBuilder('s')
            ->addSelect('i')
            ->addSelect('u')
            ->innerJoin('s.item', 'i')
            ->innerJoin('i.unit', 'u')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
        try {
            /** @phpstan-ignore-next-line */
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
