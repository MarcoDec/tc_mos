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

    public function countGrouped(Warehouse $warehouse): int {
        /** @phpstan-ignore-next-line */
        return $this->_em->getConnection()->executeQuery(
            sql: 'SELECT COUNT(*) FROM `stock_grouped` WHERE `warehouse_id` = :warehouse',
            params: ['warehouse' => $warehouse->getId()]
        )->fetchOne();
    }

    /**
     * @return mixed[]
     */
    public function findGrouped(Warehouse $warehouse, int $limit, int $offset): array {
        return collect($this->_em->getConnection()->executeQuery(
            sql: "SELECT * FROM `stock_grouped` WHERE `warehouse_id` = :warehouse LIMIT $limit OFFSET $offset",
            params: ['warehouse' => $warehouse->getId()]
        )->fetchAllAssociative())
            ->map(static function (array $stock): array {
                return [
                    'batchNumber' => $stock['batch_number'],
                    'item' => [
                        '@id' => $stock['@item_id'],
                        '@type' => $stock['@item_type'],
                        'id' => $stock['item_id'],
                        'code' => $stock['item_code'],
                        'name' => $stock['item_name'],
                        'unitCode' => $stock['item_unit_code'],
                    ],
                    'quantity' => [
                        'code' => $stock['quantity_code'],
                        'value' => $stock['quantity_value']
                    ],
                    'warehouse_id' => $stock['warehouse_id']
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return null|T
     */
    public function findPatch(int $id): ?Stock {
        /** @phpstan-ignore-next-line */
        return $this->_em->getRepository(ComponentStock::class)->loadPatch($id)
            ?? $this->_em->getRepository(ProductStock::class)->loadPatch($id);
    }

    /**
     * @return null|T
     */
    protected function loadPatch(int $id): ?Stock {
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
