<?php

namespace App\Repository\Manufacturing\Component;

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


final class ManufacturingComponentItemRepository extends ItemRepository {

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

    public function findByManufacturingComponentId($componentId, $tab = null) : array
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

        $sqlCount = 'SELECT COUNT(mo.id) as count ';
        $rsmCount = new ResultSetMapping();
        $rsmCount->addScalarResult('count', 'count');

        $sql =  'SELECT component.id as componentID, product.code as ref, product.forecast_volume_code as forecastVolumeCode, product.forecast_volume_value as forecastVolumeValue, mo.delivery_date as date, product.name as name '; 

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('componentID', 'componentID');
        $rsm->addScalarResult('ref', 'ref');
        $rsm->addScalarResult('name', 'name');
        $rsm->addScalarResult('forecastVolumeCode', 'forecastVolumeCode');
        $rsm->addScalarResult('forecastVolumeValue', 'forecastVolumeValue');
        $rsm->addScalarResult('date', 'date');

        $dateCourante = date('Y-m-d');
        $from = 'FROM component 
        LEFT JOIN selling_order_item AS soi ON component.id = soi.component_id 
        LEFT JOIN product_customer AS po ON soi.product_id = po.id 
        LEFT JOIN product ON product.id = po.product_id 
        LEFT JOIN manufacturing_order AS mo ON mo.id = po.id 
        WHERE mo.delivery_date >= '. $dateCourante. ' AND component.id = '. $componentId ;
        // ' WHERE poi.deleted = 0 AND po.deleted = 0 AND po.supplier_id = '. $componentId;

        $where = '';
        if ($tab['ref'] !== null && isset($tab['ref'])) {
            $where .= " AND product.code LIKE '%" . $tab['ref'] . "%'";
        }
        if ($tab['name'] !== null && isset($tab['name'])) {
            $where .= " AND product.name LIKE '%" . $tab['name'] . "%'";
        }
        if ($tab['forecastVolumeCode'] !== null && isset($tab['forecastVolumeCode'])) {
            $where .= " AND product.forecast_volume_code LIKE '%" . $tab['forecastVolumeCode'] . "%'";
        }
        if ($tab['forecastVolumeValue'] !== null && isset($tab['forecastVolumeValue'])) {
            $where .= " AND product.forecast_volume_value LIKE '%" . $tab['forecastVolumeValue'] . "%'";
        }
        if ($tab['date'] !== null && isset($tab['date'])) {
            $where .= " AND mo.delivery_date LIKE '%" . $tab['date'] . "%'";
        }

        $sqlCount .= $from .$where;
        $queryCount = $this->_em->createNativeQuery($sqlCount, $rsmCount);
        $resultsCount = $queryCount->getResult();
        $countElement = $resultsCount[0]['count'];
        if($countElement > $max){
        
            $limitCurrent = ' LIMIT ' . $currentPage . ', ' . $max;

            $sql .= $from .$where. ' ORDER BY date DESC '. $limitCurrent;
        } else {
            $sql .= $from .$where. ' ORDER BY date DESC ';
        }
        $query = $this->_em->createNativeQuery($sql, $rsm);
        $results = $query->getResult();

        foreach( $results as &$element){
            $element['@context'] = '/api/contexts/ManufacturingOrderItemComponent';
            $element['@type'] = 'ManufacturingOrderItemComponent';
            $element['@id'] =  '/api/manufacturing-order-components/' . $element['componentID'];
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
