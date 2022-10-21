<?php

namespace App\Repository\Project\Operation;

use App\Entity\Project\Operation\Type;
use App\Entity\Purchase\Component\Family;
use App\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\UnicodeString;

/**
 * @phpstan-type Criteria array{assembly?: bool, name?: string}
 * @phpstan-type Order array{name?: 'asc'|'desc'}
 *
 * @extends ServiceEntityRepository<Type>
 *
 * @method null|Type find($id, $lockMode = null, $lockVersion = null)
 * @method null|Type findOneBy(array $criteria, ?array $orderBy = null)
 * @method Type[]    findAll()
 */
final class TypeRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Type::class);
    }

    /**
     * @param Criteria   $criteria
     * @param null|Order $orderBy
     *
     * @return Type[]
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
    `t`.`deleted`,
    `t`.`id`,
    `t`.`assembly`,
    `t`.`name`,
    `f`.`deleted` AS `f_deleted`,
    `f`.`id` AS `f_id`,
    `f`.`customs_code` AS `f_customs_code`,
    `f`.`name` AS `f_name`,
    `f`.`code` AS `f_code`,
    `f`.`copperable` AS `f_copperable`,
    `f`.`parent_id` AS `f_parent_id`
FROM `operation_type` `t`
INNER JOIN ($subQuerySql) `subquery` ON `t`.`id` = `subquery`.`id`
LEFT JOIN `operation_type_component_family` `tf` ON `t`.`id` = `tf`.`type_id`
LEFT JOIN `component_family` `f`
    ON `tf`.`family_id` = `f`.`id`
    AND `f`.`deleted` = FALSE
SQL;
        if (!empty($orderBy)) {
            foreach ($orderBy as $property => $order) {
                $query .= " ORDER BY `t`.`$property` ".strtoupper($order);
            }
        }
        $rsm = new ResultSetMappingBuilder($this->_em);
        $rsm->addRootEntityFromClassMetadata($this->getClassName(), 't');
        $rsm->addJoinedEntityFromClassMetadata(
            class: Family::class,
            alias: 'f',
            parentAlias: 't',
            relation: 'families',
            renamedColumns: [
                'deleted' => 'f_deleted',
                'id' => 'f_id',
                'customs_code' => 'f_customs_code',
                'name' => 'f_name',
                'code' => 'f_code',
                'copperable' => 'f_copperable',
                'parent_id' => 'f_parent_id',
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
     *
     * @return Paginator<Type>
     */
    public function findByPaginated(int $page, array $criteria, int $limit, int $offset, ?array $orderBy = null): Paginator {
        /** @var int $total */
        $total = $this->createSubQueryBuilder($criteria, $orderBy)
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
        return new Paginator(
            items: $this->findBy($criteria, $orderBy, $limit, $offset),
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
        $qb = $this->createQueryBuilder('t')
            ->select('t.id')
            ->where('t.deleted = FALSE');
        if (isset($criteria['assembly'])) {
            $qb->andWhere('t.assembly = :assembly')->setParameter('assembly', $criteria['assembly']);
        }
        if (isset($criteria['name'])) {
            $qb->andWhere('t.name LIKE :name')->setParameter('name', "%{$criteria['name']}%");
        }
        if (!empty($orderBy)) {
            foreach ($orderBy as $property => $order) {
                $qb->orderBy("t.$property", $order);
            }
        }
        return $qb;
    }
}
