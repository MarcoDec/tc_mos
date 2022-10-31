<?php

namespace App\Repository\Logistics\Stock;

use App\Collection;
use App\Entity\Logistics\Stock\ComponentStock;
use App\Entity\Logistics\Stock\ProductStock;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Logistics\Warehouse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\UnicodeString;

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
            sql: $sql->replace('SELECT `stock_grouped`.*', 'SELECT COUNT(*)')->toString(),
            params: $params
        )->fetchOne();
    }

    /** @return mixed[] */
    final public function findGrouped(Warehouse $warehouse, int $limit, int $offset, ?string $location = null): array {
        ['params' => $params, 'sql' => $sql] = $this->createGroupedQuery($warehouse, $location);
        return Collection::collect(
            $this->_em->getConnection()
                ->executeQuery("$sql LIMIT $limit OFFSET $offset", $params)
                ->fetchAllAssociative()
        )
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
            ->all();
    }

    /** @return null|T */
    final public function findPatch(int $id): ?Stock {
        /** @phpstan-ignore-next-line */
        return $this->_em->getRepository(ComponentStock::class)->loadPatch($id)
            ?? $this->_em->getRepository(ProductStock::class)->loadPatch($id);
    }

    /** @return array{params: array{location?: string, warehouse: int|null}, sql: UnicodeString} */
    private function createGroupedQuery(Warehouse $warehouse, ?string $location = null): array {
        $sql = <<<'SQL'
WITH
    `convertible_unit` (`code`, `base`, `distance_base`) AS (
        SELECT
            `code`,
            `base`,
            IF(`base` > 1, `base`, 1 / `base`) as `distance_base`
        FROM `unit`
    ),
    `stock_component` (
        `batch_number`,
        `item_code`,
        `item_id`,
        `item_name`,
        `item_unit_code`,
        `location`,
        `quantity_code`,
        `quantity_value`,
        `warehouse_id`
    ) AS (
        SELECT
            IF(`s`.`batch_number` IS NULL, 'NULL', `s`.`batch_number`) AS `batch_number`,
            CONCAT(`f`.`code`, '-', `c`.`id`) AS `item_code`,
            `s`.`component_id` AS `item_id`,
            `c`.`name` AS `item_name`,
            `u`.`code` AS `item_unit_code`,
            `s`.`location`,
            `s`.`quantity_code`,
            `s`.`quantity_value`,
            `s`.`warehouse_id`
        FROM `stock` `s`
        INNER JOIN `component` `c`
            ON `s`.`component_id` = `c`.`id`
            AND `c`.`deleted` = FALSE
        INNER JOIN `component_family` `f`
            ON `c`.`family_id` = `f`.`id`
            AND `f`.`deleted` = FALSE
        INNER JOIN `unit` `u`
            ON `c`.`unit_id` = `u`.`id`
            AND `u`.`deleted` = FALSE
        WHERE `s`.`deleted` = FALSE
        AND `s`.`type` = 'component'
        AND `s`.`quantity_code` IS NOT NULL
        AND `s`.`quantity_value` > 0
        AND `s`.`warehouse_id` = :warehouse
        <and-location>
    ),
    `stock_component_converted` (
        `batch_number`,
        `item_code`,
        `item_id`,
        `item_name`,
        `item_unit_code`,
        `location`,
        `quantity_code`,
        `quantity_value`,
        `warehouse_id`
    ) AS (
        SELECT
            `stock_component`.`batch_number`,
            `stock_component`.`item_code`,
            `stock_component`.`item_id`,
            `stock_component`.`item_name`,
            `stock_component`.`item_unit_code`,
            `stock_component`.`location`,
            (
                SELECT `convertible_unit_min`.`code`
                FROM `convertible_unit` `convertible_unit_min`
                WHERE `convertible_unit_min`.`code` IN (
                    SELECT `convertible_unit_group`.`code`
                    FROM `stock_component` `min`
                    INNER JOIN `convertible_unit` `convertible_unit_group`
                        ON `min`.`quantity_code` = `convertible_unit_group`.`code`
                    WHERE `min`.`item_id` = `stock_component`.`item_id`
                )
                AND `convertible_unit_min`.`base` IN (
                    SELECT MIN(`convertible_unit_group`.`base`)
                    FROM `stock_component` `min`
                    INNER JOIN `convertible_unit` `convertible_unit_group`
                        ON `min`.`quantity_code` = `convertible_unit_group`.`code`
                    WHERE `min`.`item_id` = `stock_component`.`item_id`
                )
            ) AS `quantity_code`,
            `stock_component`.`quantity_value` * (
                SELECT IF(
                    `convertible_unit_min`.`code` = `stock_component`.`quantity_code`,
                    1,
                    `convertible_unit_min`.`distance_base` * `convertible_unit`.`distance_base`
                )
                FROM `convertible_unit` `convertible_unit_min`
                WHERE `convertible_unit_min`.`code` IN (
                    SELECT `convertible_unit_group`.`code`
                    FROM `stock_component` `min`
                    INNER JOIN `convertible_unit` `convertible_unit_group`
                        ON `min`.`quantity_code` = `convertible_unit_group`.`code`
                    WHERE `min`.`item_id` = `stock_component`.`item_id`
                )
                AND `convertible_unit_min`.`base` IN (
                    SELECT MIN(`convertible_unit_group`.`base`)
                    FROM `stock_component` `min`
                    INNER JOIN `convertible_unit` `convertible_unit_group`
                        ON `min`.`quantity_code` = `convertible_unit_group`.`code`
                    WHERE `min`.`item_id` = `stock_component`.`item_id`
                )
            ) AS `quantity_value`,
            `stock_component`.`warehouse_id`
        FROM `stock_component`
        INNER JOIN `convertible_unit` ON `stock_component`.`quantity_code` = `convertible_unit`.`code`
    ),
    `stock_component_grouped` (
        `warehouse_id`,
        `@item_id`,
        `@item_type`,
        `item_id`,
        `item_code`,
        `item_name`,
        `item_unit_code`,
        `batch_number`,
        `location`,
        `quantity_code`,
        `quantity_value`
    ) AS (
        SELECT
            `warehouse_id`,
            CONCAT('/api/components/', `item_id`) AS `@item_id`,
            'Component' AS `@item_type`,
            `item_id`,
            `item_code`,
            `item_name`,
            `item_unit_code`,
            `batch_number`,
            GROUP_CONCAT(DISTINCT `location` ORDER BY `location` SEPARATOR ',') AS `location`,
            `quantity_code`,
            SUM(`quantity_value`) AS `quantity_value`
        FROM `stock_component_converted`
        GROUP BY
            `warehouse_id`,
            `item_id`,
            `item_code`,
            `batch_number`
        ORDER BY
            `warehouse_id`,
            `item_id`,
            `item_code`,
            `batch_number`
    ),
    `stock_product` (
        `batch_number`,
        `item_code`,
        `item_id`,
        `item_name`,
        `item_unit_code`,
        `location`,
        `quantity_code`,
        `quantity_value`,
        `warehouse_id`
    ) AS (
        SELECT
            IF(`s`.`batch_number` IS NULL, 'NULL', `s`.`batch_number`) AS `batch_number`,
            `p`.`code` AS `item_code`,
            `s`.`product_id` AS `item_id`,
            `p`.`name` AS `item_name`,
            `u`.`code` AS `item_unit_code`,
            `s`.`location`,
            `s`.`quantity_code`,
            `s`.`quantity_value`,
            `s`.`warehouse_id`
        FROM `stock` `s`
        INNER JOIN `product` `p`
            ON `s`.`product_id` = `p`.`id`
            AND `p`.`deleted` = FALSE
        INNER JOIN `unit` `u`
            ON `p`.`unit_id` = `u`.`id`
            AND `u`.`deleted` = FALSE
        WHERE `s`.`deleted` = FALSE
        AND `s`.`type` = 'product'
        AND `s`.`quantity_code` IS NOT NULL
        AND `s`.`quantity_value` > 0
        AND `s`.`warehouse_id` = :warehouse
        <and-location>
    ),
    `stock_product_converted` (
        `batch_number`,
        `item_code`,
        `item_id`,
        `item_name`,
        `item_unit_code`,
        `location`,
        `quantity_code`,
        `quantity_value`,
        `warehouse_id`
    ) AS (
        SELECT
            `stock_product`.`batch_number`,
            `stock_product`.`item_code`,
            `stock_product`.`item_id`,
            `stock_product`.`item_name`,
            `stock_product`.`item_unit_code`,
            `stock_product`.`location`,
            (
                SELECT `convertible_unit_min`.`code`
                FROM `convertible_unit` `convertible_unit_min`
                WHERE `convertible_unit_min`.`code` IN (
                    SELECT `convertible_unit_group`.`code`
                    FROM `stock_product` `min`
                    INNER JOIN `convertible_unit` `convertible_unit_group`
                        ON `min`.`quantity_code` = `convertible_unit_group`.`code`
                    WHERE `min`.`item_id` = `stock_product`.`item_id`
                )
                AND `convertible_unit_min`.`base` IN (
                    SELECT MIN(`convertible_unit_group`.`base`)
                    FROM `stock_product` `min`
                    INNER JOIN `convertible_unit` `convertible_unit_group`
                        ON `min`.`quantity_code` = `convertible_unit_group`.`code`
                    WHERE `min`.`item_id` = `stock_product`.`item_id`
                )
            ) AS `quantity_code`,
            `stock_product`.`quantity_value` * (
                SELECT IF(
                    `convertible_unit_min`.`code` = `stock_product`.`quantity_code`,
                    1,
                    `convertible_unit_min`.`distance_base` * `convertible_unit`.`distance_base`
                )
                FROM `convertible_unit` `convertible_unit_min`
                WHERE `convertible_unit_min`.`code` IN (
                    SELECT `convertible_unit_group`.`code`
                    FROM `stock_product` `min`
                    INNER JOIN `convertible_unit` `convertible_unit_group`
                        ON `min`.`quantity_code` = `convertible_unit_group`.`code`
                    WHERE `min`.`item_id` = `stock_product`.`item_id`
                )
                AND `convertible_unit_min`.`base` IN (
                    SELECT MIN(`convertible_unit_group`.`base`)
                    FROM `stock_product` `min`
                    INNER JOIN `convertible_unit` `convertible_unit_group`
                        ON `min`.`quantity_code` = `convertible_unit_group`.`code`
                    WHERE `min`.`item_id` = `stock_product`.`item_id`
                )
            ) AS `quantity_value`,
            `stock_product`.`warehouse_id`
        FROM `stock_product`
        INNER JOIN `convertible_unit`
            ON `stock_product`.`quantity_code` = `convertible_unit`.`code`
    ),
    `stock_product_grouped` (
        `warehouse_id`,
        `@item_id`,
        `@item_type`,
        `item_id`,
        `item_code`,
        `item_name`,
        `item_unit_code`,
        `batch_number`,
        `location`,
        `quantity_code`,
        `quantity_value`
    ) AS (
        SELECT
            `warehouse_id`,
            CONCAT('/api/products/', `item_id`) AS `@item_id`,
            'Product' AS `@item_type`,
            `item_id`,
            `item_code`,
            `item_name`,
            `item_unit_code`,
            `batch_number`,
            GROUP_CONCAT(DISTINCT `location` ORDER BY `location` SEPARATOR ',') AS `location`,
            `quantity_code`,
            SUM(`quantity_value`) AS `quantity_value`
        FROM `stock_product_converted`
        GROUP BY
            `warehouse_id`,
            `item_id`,
            `item_code`,
            `batch_number`
        ORDER BY
            `warehouse_id`,
            `item_id`,
            `item_code`,
            `batch_number`
    )
SELECT `stock_grouped`.*
FROM (
    SELECT
        `warehouse_id`,
        `@item_id`,
        `@item_type`,
        `item_id`,
        `item_code`,
        `item_name`,
        `item_unit_code`,
        IF(`batch_number` LIKE 'NULL', NULL, `batch_number`) AS `batch_number`,
        `location`,
        `quantity_code`,
        `quantity_value`
    FROM `stock_component_grouped`
    UNION
    SELECT
        `warehouse_id`,
        `@item_id`,
        `@item_type`,
        `item_id`,
        `item_code`,
        `item_name`,
        `item_unit_code`,
        IF(`batch_number` LIKE 'NULL', NULL, `batch_number`) AS `batch_number`,
        `location`,
        `quantity_code`,
        `quantity_value`
    FROM `stock_product_grouped`
) AS `stock_grouped`
SQL;
        $sql = new UnicodeString($sql);
        $params = ['warehouse' => $warehouse->getId()];
        if (empty($location)) {
            $sql = $sql->replace('<and-location>', '');
        } else {
            $sql = $sql->replace('<and-location>', 'AND `s`.`location` LIKE :location');
            $params['location'] = "%$location%";
        }
        return ['params' => $params, 'sql' => $sql->replaceMatches('/\s+/', ' ')];
    }

    /** @return null|T */
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
