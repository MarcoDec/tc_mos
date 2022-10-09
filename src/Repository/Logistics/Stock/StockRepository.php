<?php

namespace App\Repository\Logistics\Stock;

use App\Entity\Logistics\Stock\ComponentStock;
use App\Entity\Logistics\Stock\ProductStock;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Logistics\Warehouse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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

    final public function countGrouped(Warehouse $warehouse, ?string $location = null): int {
        ['params' => $params, 'sql' => $sql] = $this->createGroupedQuery($warehouse, $location);
        /** @phpstan-ignore-next-line */
        return $this->_em->getConnection()->executeQuery(
            sql: str_replace('SELECT *', 'SELECT COUNT(*)', $sql),
            params: $params
        )->fetchOne();
    }

    /**
     * @return mixed[]
     */
    final public function findGrouped(Warehouse $warehouse, int $limit, int $offset, ?string $location = null): array {
        ['params' => $params, 'sql' => $sql] = $this->createGroupedQuery($warehouse, $location);
        return collect($this->_em->getConnection()->executeQuery("$sql LIMIT $limit OFFSET $offset", $params)->fetchAllAssociative())
            ->map(static fn (array $stock): array => [
                'batchNumber' => $stock['batch_number'],
                'item' => [
                    '@id' => $stock['@item_id'],
                    '@type' => $stock['@item_type'],
                    'id' => $stock['item_id'],
                    'code' => $stock['item_code'],
                    'name' => $stock['item_name'],
                    'unitCode' => $stock['item_unit_code'],
                ],
                'location' => $stock['location'],
                'quantity' => [
                    'code' => $stock['quantity_code'],
                    'value' => $stock['quantity_value']
                ],
                'warehouse_id' => $stock['warehouse_id']
            ])
            ->values()
            ->all();
    }

    /**
     * @return null|T
     */
    final public function findPatch(int $id): ?Stock {
        /** @phpstan-ignore-next-line */
        return $this->_em->getRepository(ComponentStock::class)->loadPatch($id)
            ?? $this->_em->getRepository(ProductStock::class)->loadPatch($id);
    }

    /**
     * @return array{params: array{location?: string, warehouse: int|null}, sql: string}
     */
    private function createGroupedQuery(Warehouse $warehouse, ?string $location = null): array {
        $sql = 'SELECT * FROM `stock_grouped` WHERE `warehouse_id` = :warehouse';
        $params = ['warehouse' => $warehouse->getId()];
        if (!empty($location)) {
            $sql .= ' AND `location` LIKE :location';
            $params['location'] = "%$location%";
        }
        return ['params' => $params, 'sql' => $sql];
    }

    /**
     * @return null|T
     */
    private function loadPatch(int $id): ?Stock {
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
            $stock = $query->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            $stock = null;
        }
        /** @var null|T $stock */
        if (empty($stock)) {
            return $stock;
        }
        $receipts = $stock->getReceipts();
        if (method_exists($receipts, 'setInitialized')) {
            $receipts->setInitialized(true);
        }
        return $stock;
    }
}
