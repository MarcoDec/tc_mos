<?php

namespace App\Repository\Production\Quality;

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


final class ItemProductionQualityComponentRepository extends ItemRepository {

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

    public function findOneByReceipt(int $id, string $resourceClass): ?ComponentItem {
        $query = $this->createReceiptQueryBuilder($id)->getQuery();
        try {
            /** @phpstan-ignore-next-line */
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }
    }

    public function findByQualityComponentId($componentId, $tab = null) : array
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

        $sqlCount = 'SELECT COUNT(pq.id) as count ';
        $rsmCount = new ResultSetMapping();
        $rsmCount->addScalarResult('count', 'count');

        $sql = 'SELECT pq.id as id, pq.record_date as creeLe, employee.name as detecteParName, employee.surname as detecteParSurname, component.manufacturer_code as ref, pq.comment as description, resp.name as responsableName, resp.surname as responsableSurname, zone.name as localisation, company.name as societe, moperation.quantity_produced_value as progressionValue, moperation.quantity_produced_code as progressionCode, moperation.emb_state_state as statut ';
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('creeLe', 'creeLe');
        $rsm->addScalarResult('detecteParName', 'detecteParName');
        $rsm->addScalarResult('detecteParSurname', 'detecteParSurname');
        $rsm->addScalarResult('ref', 'ref');
        $rsm->addScalarResult('description', 'description');
        $rsm->addScalarResult('responsableName', 'responsableName');
        $rsm->addScalarResult('responsableSurname', 'responsableSurname');
        $rsm->addScalarResult('societe', 'societe');
        $rsm->addScalarResult('localisation', 'localisation');
        $rsm->addScalarResult('progressionValue', 'progressionValue');
        $rsm->addScalarResult('progressionCode', 'progressionCode');
        $rsm->addScalarResult('statut', 'statut');

        $from = ' FROM production_quality as pq 
        LEFT JOIN employee ON pq.employee_id = employee.id
        LEFT JOIN manufacturing_operation as moperation ON moperation.id = pq.production_operation_id
        LEFT JOIN employee as resp ON moperation.pic_id = resp.id
        LEFT JOIN zone ON  zone.id = moperation.zone_id
        LEFT JOIN company ON company.id = zone.company_id
        LEFT JOIN manufacturing_order as morder ON moperation.order_id = morder.id
        LEFT JOIN selling_order as so ON so.id = morder.selling_order_id
        LEFT JOIN selling_order_item as soi ON soi.id = so.id
        LEFT JOIN component ON component.id = soi.component_id
        WHERE component.id = '. $componentId .' AND pq.deleted = 0';

        $where = '';
        if ($tab['creeLe'] !== null && isset($tab['creeLe'])) {
            $where .= " AND pq.record_date LIKE '%" . $tab['creeLe'] . "%'";
        }
        if ($tab['ref'] !== null && isset($tab['ref'])) {
            $where .= " AND component.manufacturer_code LIKE '%" . $tab['ref'] . "%'";
        }
        if ($tab['detectePar'] !== null && isset($tab['detectePar'])) {
            $detectePar = explode(' ', $tab['detectePar']);
            if (count($detectePar) === 1) {
                $where .= " AND (employee.name LIKE '%" . $detectePar[0] . "%'
                            OR employee.surname LIKE '%" . $detectePar[0] . "%')";
            } else {
                $where .= " AND employee.name LIKE '%" . $detectePar[0] . "%'";
                $where .= " AND employee.surname LIKE '%" . $detectePar[1] . "%'";
            }
        }
        
        if ($tab['description'] !== null && isset($tab['description'])) {
            $where .= " AND pq.comment LIKE '%" . $tab['description'] . "%'";
        }
        
        if ($tab['responsable'] !== null && isset($tab['responsable'])) {
            $responsable = explode(' ', $tab['responsable']);
            if (count($responsable) === 1) {
                $where .= " AND (resp.name LIKE '%" . $responsable[0] . "%'
                            OR resp.surname LIKE '%" . $responsable[0] . "%')";
            } else {
                $where .= " AND resp.name LIKE '%" . $responsable[0] . "%'";
                $where .= " AND resp.surname LIKE '%" . $responsable[1] . "%'";
            }
        }
        if ($tab['localisation'] !== null && isset($tab['localisation'])) {
            $where .= " AND zone.name LIKE '%" . $tab['localisation'] . "%'";
        }
        if ($tab['societe'] !== null && isset($tab['societe'])) {
            $where .= " AND company.name LIKE '%" . $tab['societe'] . "%'";
        }
        if ($tab['progressionCode'] !== null && isset($tab['progressionCode'])) {
            $where .= " AND moperation.quantity_produced_code LIKE '%" . $tab['progressionCode'] . "%'";
        }
        if ($tab['progressionValue'] !== null && isset($tab['progressionValue'])) {
            $where .= " AND moperation.quantity_produced_value LIKE '%" . $tab['progressionValue'] . "%'";
        }
        if ($tab['statut'] !== null && isset($tab['statut'])) {
            $where .= " AND moperation.emb_state_state LIKE '%" . $tab['statut'] . "%'";
        }

        $sqlCount .= $from .$where;
        $queryCount = $this->_em->createNativeQuery($sqlCount, $rsmCount);
        $resultsCount = $queryCount->getResult();
        $countElement = $resultsCount[0]['count'];
        if($countElement > $max){
            $min = '';
            if($currentPage != 1 ){
                $min =  ($currentPage -1) *$max;
            } else {
                $min = $currentPage;
            }
            $limitCurrent = ' LIMIT ' . $min. ', ' . $max;

            $sql .= $from .$where. ' ORDER BY creeLe DESC '. $limitCurrent;
        } else {
            $sql .= $from .$where. ' ORDER BY creeLe DESC ';
        }
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $results = $query->getResult();

        foreach( $results as &$element){
            $element['@context'] = '/api/contexts/QualityOrderItemComponent';
            $element['@type'] = 'QualityOrderItemComponent';
            $element['@id'] =  '/api/id/'. $element['id'];
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
//        dump($list);
        return $list;
    }
}
