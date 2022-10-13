<?php

namespace App\Repository\Purchase\Component;

use App\Entity\Purchase\Component\Attribute;
use App\Entity\Purchase\Component\Family;
use App\Paginator\Purchase\Component\AttributePaginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\UnicodeString;

/**
 * @phpstan-type Criteria array{description?: string, name?: string, type?: string, unit?: \App\Entity\Management\Unit}
 * @phpstan-type Order array<'name'|'type'|'unit.name', 'asc'|'desc'>
 *
 * @extends ServiceEntityRepository<Attribute>
 *
 * @method Attribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attribute|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Attribute[]    findAll()
 */
final class AttributeRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Attribute::class);
    }

    /**
     * @param Criteria   $criteria
     * @param null|Order $orderBy
     *
     * @return Attribute[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        $subQuery = $this->createSubQueryBuilder($criteria, $orderBy)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();
        $subQueryParams = $subQuery->getParameters();
        $params = [];
        /** @var string $subQuerySql */
        $subQuerySql = preg_replace('/(\w+\.id) AS \w+/', '$1', $subQuery->getSQL());
        foreach ($subQueryParams as $param) {
            /** @var string $subQuerySql */
            $subQuerySql = preg_replace('/\?/', ":{$param->getName()}", $subQuerySql, 1);
            $params[$param->getName()] = $param->getValue();
        }
        $query = <<<SQL
SELECT
    `a`.`deleted`,
    `a`.`id`,
    `a`.`description`,
    `a`.`name`,
    `a`.`type`,
    `a`.`unit_id`,
    `f`.`deleted` AS `f_deleted`,
    `f`.`id` AS `f_id`,
    `f`.`customs_code` AS `f_customs_code`,
    `f`.`name` AS `f_name`,
    `f`.`code` AS `f_code`,
    `f`.`copperable` AS `f_copperable`,
    `f`.`parent_id` AS `f_parent_id`
FROM `attribute` `a`
INNER JOIN ($subQuerySql) `subquery` ON `a`.`id` = `subquery`.`id`
LEFT JOIN `attribute_family` `af` ON `a`.`id` = `af`.`attribute_id`
LEFT JOIN `component_family` `f`
    ON `af`.`family_id` = `f`.`id`
    AND `f`.`deleted` = FALSE
SQL;
        if (!empty($orderBy)) {
            foreach ($orderBy as $property => $order) {
                $query .= " ORDER BY `a`.`$property` ".strtoupper($order);
            }
        }
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata($this->getClassName(), 'a');
        $rsm->addJoinedEntityFromClassMetadata(
            class: Family::class,
            alias: 'f',
            parentAlias: 'a',
            relation: 'families',
            renamedColumns: [
                'deleted' => 'f_deleted',
                'id' => 'f_id',
                'customs_code' => 'f_customs_code',
                'name' => 'f_name',
                'code' => 'f_code',
                'copperable' => 'f_copperable',
                'parent_id' => 'f_parent_id'
            ]
        );
        /** @phpstan-ignore-next-line */
        return $this->_em
            ->createNativeQuery(
                sql: (new UnicodeString($query))->replaceMatches('/\s+/', ' ')->toString(),
                rsm: $rsm
            )
            ->setParameters($params)
            ->getResult();
    }

    /**
     * @param Criteria   $criteria
     * @param null|Order $orderBy
     */
    public function findByPaginated(int $page, array $criteria, int $limit, int $offset, ?array $orderBy = null): AttributePaginator {
        /** @var int $total */
        $total = $this->createSubQueryBuilder($criteria, $orderBy)
            ->select('COUNT(i.id)')
            ->getQuery()
            ->getSingleScalarResult();
        return new AttributePaginator(
            attributes: $this->findBy($criteria, $orderBy, $limit, $offset),
            itemPerPage: $limit,
            page: $page,
            total: $total
        );
    }

    /**
     * @param Criteria   $criteria
     * @param null|Order $orderBy
     */
    private function createSubQueryBuilder(array $criteria, ?array $orderBy = null): QueryBuilder {
        $qb = $this->createQueryBuilder('i')
            ->select('i.id')
            ->where('i.deleted = FALSE');
        if (isset($criteria['description'])) {
            $qb->andWhere('i.description LIKE :description')->setParameter('description', "%{$criteria['description']}%");
        }
        if (isset($criteria['name'])) {
            $qb->andWhere('i.name LIKE :name')->setParameter('name', "%{$criteria['name']}%");
        }
        if (isset($criteria['unit'])) {
            $qb->andWhere('i.unit = :unit')->setParameter('unit', $criteria['unit']->getId());
        }
        if (isset($criteria['type'])) {
            $qb->andWhere('i.type LIKE :type')->setParameter('type', $criteria['type']);
        }
        if (!empty($orderBy)) {
            foreach ($orderBy as $property => $order) {
                $qb->orderBy("i.$property", $order);
            }
        }
        return $qb;
    }
}
