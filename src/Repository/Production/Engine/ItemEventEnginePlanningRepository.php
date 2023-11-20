<?php

namespace App\Repository\Production\Engine;

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


final class ItemEventEnginePlanningRepository extends ItemRepository {

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

 
    public function findByEventEngineId($engineId, $tab = null) : array
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

        $sqlCount = 'SELECT COUNT(engine_event.id) as count ';
        $rsmCount = new ResultSetMapping();
        $rsmCount->addScalarResult('count', 'count');

        $sql = 'SELECT engine_event.id as id, engine_event.date as date, engine_event.type as type, pl.name as name, pl.quantity_code as quantiteCode, pl.quantity_value as quantiteValue ';  
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('date', 'date');
        $rsm->addScalarResult('name', 'name');
        $rsm->addScalarResult('quantiteCode', 'quantiteCode');
        $rsm->addScalarResult('quantiteValue', 'quantiteValue');
        $rsm->addScalarResult('type', 'type');


        $currentDate = date('Y-m-d H:i:s');

        $from = ' FROM engine_event
        LEFT JOIN planning as pl ON pl.id = engine_event.planned_by_id 
        WHERE engine_event.engine_id = '. $engineId .' AND engine_event.deleted = 0 AND engine_event.date >= "'. $currentDate . '"';
        
        $where = '';
        if ($tab['date'] !== null && isset($tab['date'])) {
            $where .= " AND engine_event.date LIKE '%" . $tab['date'] . "%'";
        }
        if ($tab['name'] !== null && isset($tab['name'])) {
            $where .= " AND pl.name LIKE '%" . $tab['name'] . "%'";
        }
        if ($tab['quantiteValue'] !== null && isset($tab['quantiteValue'])) {
            $where .= " AND pl.quantity_value LIKE '%" . $tab['quantiteValue'] . "%'";
        }
        if ($tab['quantiteCode'] !== null && isset($tab['quantiteCode'])) {
            $where .= " AND pl.quantity_code LIKE '%" . $tab['quantiteCode'] . "%'";
        }
        if ($tab['type'] !== null && isset($tab['type'])) {
            $where .= " AND engine_event.type LIKE '%" . $tab['type'] . "%'";
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
            $sql .= $from .$where. ' ORDER BY date ASC '. $limitCurrent;
        } else {
            $sql .= $from .$where. ' ORDER BY date ASC ';
        }
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $results = $query->getResult();

        foreach( $results as &$element){
            $element['@context'] = '/api/contexts/EventEquipementEventItem';
            $element['@type'] = 'EventEquipementEventItem';
            $element['@id'] =  '/api/idEventEngine/'. $element['id'];
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
