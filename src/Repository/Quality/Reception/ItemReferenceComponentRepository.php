<?php

namespace App\Repository\Quality\Reception;

use App\Entity\Purchase\Order\ComponentItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Purchase\Order\Order;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\ResultSetMapping;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Copper;
use App\Entity\Embeddable\Blocker;
use App\Entity\Embeddable\EventState;
use App\Repository\Purchase\Order\ItemRepository;
/**
 * @extends ItemRepository<ComponentItem>
 *
 * @method ComponentItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComponentItem|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method ComponentItem[]    findAll()
 */


final class ItemReferenceComponentRepository extends ItemRepository {

    const ITEMS_PER_PAGE = 15;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ComponentItem::class);
    }

    public function createByQueryBuilder(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): QueryBuilder {
        return parent::createByQueryBuilder($criteria, $orderBy, $limit, $offset)
            ->addSelect('item')
            ->addSelect('item_family')
            ->addSelect('u')
            ->leftJoin('i.item', 'item', Join::WITH, 'item.deleted = FALSE')
            ->leftJoin('item.family', 'item_family', Join::WITH, 'item_family.deleted = FALSE')
            ->leftJoin('item.unit', 'u', Join::WITH, 'u.deleted = FALSE');
    }

    public function createCheckQueryBuilder(int $id): QueryBuilder {
        return parent::createCheckQueryBuilder($id)
            ->addSelect('family_references')
            ->addSelect('item')
            ->addSelect('item_family')
            ->addSelect('item_references')
            ->addSelect('u')
            ->leftJoin('i.item', 'item', Join::WITH, 'item.deleted = FALSE')
            ->leftJoin('item.family', 'item_family', Join::WITH, 'item_family.deleted = FALSE')
            ->leftJoin('item_family.references', 'family_references', Join::WITH, 'family_references.deleted = FALSE')
            ->leftJoin('item.references', 'item_references', Join::WITH, 'item_references.deleted = FALSE')
            ->leftJoin('item.unit', 'u', Join::WITH, 'u.deleted = FALSE');
    }

    public function createReceiptQueryBuilder(int $id): QueryBuilder {
        return parent::createReceiptQueryBuilder($id)
            ->addSelect('item')
            ->addSelect('u')
            ->leftJoin('i.item', 'item', Join::WITH, 'item.deleted = FALSE')
            ->leftJoin('item.unit', 'u', Join::WITH, 'u.deleted = FALSE');
    }

    /**
     * @return ComponentItem[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        /** @phpstan-ignore-next-line */
        return $this->createByQueryBuilder($criteria, $orderBy, $limit, $offset)->getQuery()->getResult();
    }

    public function findOneByCheck(int $id): ?ComponentItem {
        $query = $this->createCheckQueryBuilder($id)->getQuery();
        try {
            /** @phpstan-ignore-next-line */
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }
    }

    public function findOneByReceipt(int $id, string $ressourceClass): ?ComponentItem {
        $query = $this->createReceiptQueryBuilder($id)->getQuery();
        try {
            /** @phpstan-ignore-next-line */
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }
    }

    public function findByReferenceComponentId($componentId, $tab = null) : array
    {

        $currentPage = $tab['currentPage'];
        // $first = 0;
        $max = 15;
        if ($currentPage !== null && $currentPage !== '1' && isset($currentPage)){
            // $currentPage = 15 * $currentPage -1;
            $currentPage = $currentPage;
        } else{
            // $currentPage = 0;
            $currentPage = 1;
        }

        $sqlCount = 'SELECT COUNT(reference.id) as count ';
        $rsmCount = new ResultSetMapping();
        $rsmCount->addScalarResult('count', 'count');

        $sql =  'SELECT reference.id as id, reference.name as name, reference.kind as kind, reference.sample_quantity as sampleQuantity, reference.min_value_value as minValue, reference.min_value_code as minCode, reference.max_value_value as maxValeur, reference.max_value_code as maxCode ';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('kind', 'kind');
        $rsm->addScalarResult('sampleQuantity', 'sampleQuantity');
        $rsm->addScalarResult('name', 'name');
        $rsm->addScalarResult('minCode', 'minCode');
        $rsm->addScalarResult('minValue', 'minValue');
        $rsm->addScalarResult('maxCode', 'maxCode');
        $rsm->addScalarResult('maxValeur', 'maxValeur');

        $from = ' FROM component_reference_component
        LEFT JOIN reference ON reference.id = component_reference_component.component_reference_id 
        WHERE reference.deleted = 0 AND component_reference_component.component_id = '. $componentId ;

        $where = '';
        if ($tab['kind'] !== null && isset($tab['kind'])) {
            $where .= " AND reference.kind LIKE '%" . $tab['kind'] . "%'";
        }
        if ($tab['name'] !== null && isset($tab['name'])) {
            $where .= " AND reference.name LIKE '%" . $tab['name'] . "%'";
        }
        if ($tab['sampleQuantity'] !== null && isset($tab['sampleQuantity'])) {
            $where .= " AND reference.sample_quantity LIKE '%" . $tab['sampleQuantity'] . "%'";
        }
        if ($tab['minCode'] !== null && isset($tab['minCode'])) {
            $where .= " AND reference.min_value_code LIKE '%" . $tab['minCode'] . "%'";
        }
        if ($tab['minValue'] !== null && isset($tab['minValue'])) {
            $where .= " AND reference.min_value_value LIKE '%" . $tab['minValue'] . "%'";
        }
        if ($tab['maxCode'] !== null && isset($tab['maxCode'])) {
            $where .= " AND reference.max_value_code LIKE '%" . $tab['maxCode'] . "%'";
        }
        if ($tab['maxValeur'] !== null && isset($tab['maxValeur'])) {
            $where .= " AND reference.max_value_value LIKE '%" . $tab['maxValeur'] . "%'";
        }

        $sqlCount .= $from .$where;
        dump($sqlCount);
        $queryCount = $this->_em->createNativeQuery($sqlCount, $rsmCount);
        $resultsCount = $queryCount->getResult();
        $countElement = $resultsCount[0]['count'];
        if($countElement > $max){
        
            $limitCurrent = ' LIMIT ' . $currentPage . ', ' . $max;

            $sql .= $from .$where. $limitCurrent;
        } else {
            $sql .= $from .$where;
        }
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $results = $query->getResult();

        foreach( $results as &$element){
            $element['@context'] = '/api/contexts/ReferenceItemComponent';
            $element['@type'] = 'ReferenceItemComponent';
            $element['@id'] =  '/api/reference-components/' . $element['id'];
        };
        $list = [];
        if($countElement > $max){
            $nbPage = intval(ceil($countElement / $max));

            $list = [
                'page' => $currentPage,
                'nbPage' => $nbPage,
                'current' => $results
            ];
        } else { 
            $list = [
                'page' => $currentPage,
                'nbPage' => 1,
                'current' => $results
            ];
        };

        return $list;
    }
}
