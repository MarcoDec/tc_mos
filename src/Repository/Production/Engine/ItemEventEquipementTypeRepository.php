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


final class ItemEventEquipementTypeRepository extends ItemRepository {

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

        $sql = 'SELECT engine_event.id as id, engine_event.date as date, engine_event.done as done, engine_event.emergency as emergency, engine_event.emb_state_state as etat, engine_event.intervention_notes as interventionNotes, engine_event.type as type';  
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('date', 'date');
        $rsm->addScalarResult('done', 'done');
        $rsm->addScalarResult('emergency', 'emergency');
        $rsm->addScalarResult('etat', 'etat');
        $rsm->addScalarResult('interventionNotes', 'interventionNotes');
        $rsm->addScalarResult('type', 'type');

        $from = ' FROM engine_event
        WHERE engine_id = '. $engineId .' AND engine_event.deleted = 0';

        $where = '';
        if ($tab['date'] !== null && isset($tab['date'])) {
            $where .= " AND engine_event.date LIKE '%" . $tab['date'] . "%'";
        }
        if ($tab['done'] !== null && isset($tab['done'])) {
            $where .= " AND engine_event.done LIKE '%" . $tab['done'] . "%'";
        }
        if ($tab['emergency'] !== null && isset($tab['emergency'])) {
            $where .= " AND engine_event.emergency LIKE '%" . $tab['emergency'] . "%'";
        }
        if ($tab['etat'] !== null && isset($tab['etat'])) {
            $where .= " AND engine_event.emb_state_state LIKE '%" . $tab['etat'] . "%'";
        }
        if ($tab['interventionNotes'] !== null && isset($tab['interventionNotes'])) {
            $where .= " AND engine_event.intervention_notes LIKE '%" . $tab['interventionNotes'] . "%'";
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
            $sql .= $from .$where. ' ORDER BY date DESC '. $limitCurrent;
        } else {
            $sql .= $from .$where. ' ORDER BY date DESC ';
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
        dump($list);
        return $list;
    }
}
