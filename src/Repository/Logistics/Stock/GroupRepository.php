<?php

namespace App\Repository\Logistics\Stock;

use App\Collection;
use App\Entity\Logistics\Stock\Group;
use App\Entity\Logistics\Stock\Stock;
use App\Entity\Logistics\Warehouse;
use App\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\UnicodeString;

/** @extends ServiceEntityRepository<Group> */
final class GroupRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Stock::class);
    }

    /**
     * @param array{location: null|string, warehouse: Warehouse} $criteria
     *
     * @return Group[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        ['params' => $params, 'sql' => $sql] = $this->createByQuery($criteria['warehouse'], $criteria['location'] ?? null);
        /** @param array{batch_number: null|string, id: string, item_code: string, item_id: int, item_name: string, item_unit_code: string, location: null|string, quantity_code: string, quantity_value: float, warehouse_id: int} $group */
        $mapper = static fn (array $group): Group => new Group(
            batchNumber: $group['batch_number'],
            id: $group['id'],
            item: [
                '@id' => $group['@item_id'],
                '@type' => $group['@item_type'],
                'id' => $group['item_id'],
                'code' => $group['item_code'],
                'name' => $group['item_name'],
                'unitCode' => $group['item_unit_code']
            ],
            location: $group['location'],
            quantity: [
                'code' => $group['quantity_code'],
                'value' => $group['quantity_value']
            ],
            warehouse: $group['warehouse_id']
        );
        return Collection::collect(
            $this->_em->getConnection()
                ->executeQuery("$sql LIMIT $limit OFFSET $offset", $params)
                ->fetchAllAssociative()
        )
            ->map($mapper)
            ->all();
    }

    /** @return Paginator<Group> */
    public function findByPaginated(int $page, int $limit, int $offset, Warehouse $warehouse, ?string $location = null): Paginator {
        return new Paginator(
            items: $this->findBy(criteria: ['location' => $location, 'warehouse' => $warehouse], limit: $limit, offset: $offset),
            itemPerPage: $limit,
            page: $page,
            total: $this->countBy($warehouse, $location)
        );
    }

    private function countBy(Warehouse $warehouse, ?string $location = null): int {
        ['params' => $params, 'sql' => $sql] = $this->createByQuery($warehouse, $location);
        /** @phpstan-ignore-next-line */
        return $this->_em->getConnection()->executeQuery(
            sql: $sql->replace('SELECT `stock_grouped`.*', 'SELECT COUNT(*)')->toString(),
            params: $params
        )->fetchOne();
    }

    /** @return array{params: array{location?: string, warehouse: int|null}, sql: UnicodeString} */
    private function createByQuery(Warehouse $warehouse, ?string $location = null): array {
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
        `id`,
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
            CONCAT(`warehouse_id`, '-component-', `item_id`, '-', `batch_number`) AS `id`,
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
        `id`,
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
            CONCAT(`warehouse_id`, '-product-', `item_id`, '-', `batch_number`) AS `id`,
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
        `id`,
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
        `id`,
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
}
