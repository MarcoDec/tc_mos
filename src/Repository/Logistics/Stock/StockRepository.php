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
     * @param array<string, mixed> $filters
     *
     * @return (ComponentStock|ProductStock)[]
     */
    public function findFetched(array $filters): array {
        /** @var (ComponentStock|ProductStock)[] $componentsStocks */
        $componentsStocks = $this->_em->getRepository(ComponentStock::class)->findFetched($filters);
        /** @var (ComponentStock|ProductStock)[] $productStocks */
        $productStocks = $this->_em->getRepository(ProductStock::class)->findFetched($filters);
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

    /**
     * @param array<string, mixed> $filters
     */
    protected function createFetchedQueryBuilder(array $filters): QueryBuilder {
        $qb = $this->createQueryBuilder('s');
        foreach ($filters as $property => $value) {
            if ($property === 'warehouse' && $value instanceof Warehouse) {
                $qb->where('s.warehouse = :warehouse')->setParameter('warehouse', $value->getId());
            }
        }
        return $qb;
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
