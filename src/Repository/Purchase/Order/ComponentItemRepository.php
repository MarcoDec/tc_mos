<?php

namespace App\Repository\Purchase\Order;

use App\Entity\Purchase\Order\ComponentItem;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use App\Entity\Embeddable\Measure;
use App\Entity\Embeddable\Copper;
use App\Entity\Embeddable\Blocker;
use App\Entity\Embeddable\EventState;

/**
 * @extends ItemRepository<ComponentItem>
 *
 * @method ComponentItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComponentItem|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method ComponentItem[]    findAll()
 */
final class ComponentItemRepository extends ItemRepository {

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

//    /**
//     * @return ComponentItem[]
//     */
//    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
//        /** @phpstan-ignore-next-line */
//        return $this->createByQueryBuilder($criteria, $orderBy, $limit, $offset)->getQuery()->getResult();
//    }

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

    public function findBySupplierId($supplierId, $tab = null) : array
    {

        $currentPage = $tab['currentPage'];
        // $first = 0;
        $max = 15;
        if (!isset($currentPage) && $currentPage !== '1') $currentPage = 1;

        $sqlCount = 'SELECT COUNT(poi.id) as count ';
        $rsmCount = new ResultSetMapping();
        $rsmCount->addScalarResult('count', 'count');

        $sql =  'SELECT DISTINCT poi.id as id, 
        poi.component_id as component, 
        poi.confirmed_date as confirmedDate, '.
        'poi.confirmed_quantity_code as confirmedQuantityCode, 
        poi.confirmed_quantity_denominator as confirmedQuantityDenominator, 
        poi.confirmed_quantity_value as confirmedQuantityValue, ' .
        'poi.copper_price_code as copperPriceCode, 
        poi.copper_price_denominator as copperPriceDenominator, 
        poi.copper_price_value as copperPriceValue, '.
        'poi.emb_blocker_state as embBlockerState, 
        poi.emb_state_state as embStateState, 
        poi.notes as notes, '.
        'poi.parent_order_id as parentOrder, 
        poi.price_code as priceCode, 
        poi.price_denominator as priceDenominator, 
        poi.price_value as priceValue, '.
        'poi.product_id as product, 
        poi.ref as ref, 
        poi.requested_date as requestedDate, 
        poi.requested_quantity_code as requestedQuantityCode, 
        poi.requested_quantity_denominator as requestedQuantityDenominator, 
        poi.requested_quantity_value as requestedQuantityValue, '.
        'poi.target_company_id as targetCompany, 
        poi.type as type , 
        po.id as idOrder,
         po.company_id as company, 
         po.contact_id as contact, 
         po.delivery_company_id as deliveryCompany, 
         po.emb_blocker_state as embBlockerStatepo, 
         po.emb_state_state as embStateStatepo, 
         po.notes as notesPo, po.id as orderIdPo, '.
        'po.ref as refPo, 
        po.supplement_fret as supplementFret, 
        po.supplier_id as supplier, '.
        'component.id as idComp , 
        cf.code as codeComp ';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('component', 'component');
        $rsm->addScalarResult('confirmedDate', 'confirmedDate');
        $rsm->addScalarResult('confirmedQuantityCode', 'confirmedQuantityCode');
        $rsm->addScalarResult('confirmedQuantityDenominator', 'confirmedQuantityDenominator');
        $rsm->addScalarResult('confirmedQuantityValue', 'confirmedQuantityValue');
        $rsm->addScalarResult('copperPriceCode', 'copperPriceCode');
        $rsm->addScalarResult('copperPriceDenominator', 'copperPriceDenominator');
        $rsm->addScalarResult('copperPriceValue', 'copperPriceValue');
        $rsm->addScalarResult('embBlockerState', 'embBlockerState');
        $rsm->addScalarResult('embStateState', 'embStateState');
        $rsm->addScalarResult('notes', 'notes');
        $rsm->addScalarResult('parentOrder', 'parentOrder');
        $rsm->addScalarResult('priceCode', 'priceCode');
        $rsm->addScalarResult('priceDenominator', 'priceDenominator');
        $rsm->addScalarResult('priceValue', 'priceValue');
        $rsm->addScalarResult('product', 'product');
        $rsm->addScalarResult('ref', 'ref');
        $rsm->addScalarResult('requestedDate', 'requestedDate');
        $rsm->addScalarResult('requestedQuantityCode', 'requestedQuantityCode');
        $rsm->addScalarResult('requestedQuantityDenominator', 'requestedQuantityDenominator');
        $rsm->addScalarResult('requestedQuantityValue', 'requestedQuantityValue');
        $rsm->addScalarResult('targetCompany', 'targetCompany');
        $rsm->addScalarResult('type', 'type');
        $rsm->addScalarResult('idOrder', 'idOrder');
        $rsm->addScalarResult('company', 'company');
        $rsm->addScalarResult('contact', 'contact');
        $rsm->addScalarResult('deliveryCompany', 'deliveryCompany');
        $rsm->addScalarResult('embBlockerStatepo', 'embBlockerStatepo');
        $rsm->addScalarResult('embStateStatepo', 'embStateStatepo');
        $rsm->addScalarResult('notesPo', 'notesPo');
        $rsm->addScalarResult('orderIdPo', 'orderIdPo');
        $rsm->addScalarResult('refPo', 'refPo');
        $rsm->addScalarResult('supplementFret', 'supplementFret');
        $rsm->addScalarResult('supplier', 'supplier');
        $rsm->addScalarResult('idComp', 'idComp');
        $rsm->addScalarResult('codeComp', 'codeComp');

        $from = 'FROM purchase_order_item as poi '.
        ' LEFT JOIN purchase_order as po ON poi.parent_order_id = po.id '.
        ' LEFT JOIN component ON poi.component_id = component.id AND component.deleted = 0 '.
        ' LEFT JOIN component_family AS cf ON component.family_id = cf.id AND cf.deleted = 0 '.
        ' WHERE poi.deleted = 0 AND po.deleted = 0 AND po.supplier_id = '. $supplierId;

        $where = '';

        if ($tab['item'] !== null && isset($tab['item'])){
            $nbItem = explode('/', $tab['item']);
            $nbItem = $nbItem[3];
            $where .= ' AND poi.component_id = '. $nbItem;
        }
        if ($tab['confQuantityCode'] !== null && isset($tab['confQuantityCode'])) {
            $where .= " AND poi.confirmed_quantity_code LIKE '%" . $tab['confQuantityCode'] . "%'";
        }
        if ($tab['confQuantityValue'] !== null && isset($tab['confQuantityvalue'])) {
            $where .= " AND poi.confirmed_quantity_value LIKE '%" . $tab['confQuantityValue'] . "%'";
        }
        if ($tab['reqQuantityCode'] !== null && isset($tab['reqQuantityCode'])) {
            $where .= " AND poi.requested_quantity_code LIKE '%" . $tab['reqQuantityCode'] . "%'";
        }
        if ($tab['reqQuantityValue'] !== null && isset($tab['reqQuantityValue'])) {
            $where .= " AND poi.requested_quantity_value LIKE '%" . $tab['reqQuantityValue'] . "%'";
        }
        if ($tab['confDate'] !== null && isset($tab['confDate'])) {
            $where .= " AND poi.confirmed_date LIKE '%" . $tab['confDate'] . "%'";
        }
        if ($tab['reqDate'] !== null && isset($tab['reqDate'])) {
            $where .= " AND poi.requested_date LIKE '%" . $tab['reqDate'] . "%'";
        }
        if ($tab['note'] !== null && isset($tab['note'])) {
            $where .= " AND poi.notes LIKE '%" . $tab['note'] . "%'";
        }

        if ($tab['retard'] !== null && isset($tab['retard'])) {
            if (strcasecmp($tab['retard'], 'aucun') === 0 || $tab['retard'] === '0') {
                $where .= ' AND poi.requested_quantity_value >= poi.confirmed_quantity_value';
            } else{
                $retard = $tab['retard'];

                $parts = explode(' ', $retard);

                $retard = array('0', '0', '0');
                foreach ($parts as $part) {
                    if (strpos($part, 'y') !== false) {
                        $retard[0] = trim($part, 'y');
                    } elseif (strpos($part, 'm') !== false) {
                        $retard[1] = trim($part, 'm');
                    } elseif (strpos($part, 'd') !== false) {
                        $retard[2] = trim($part, 'd');
                    }
                }
                $retard = implode('-', $retard);

                $partRetard = explode('-', $retard);
                $partRetard = array_map('intval', $partRetard);
                $currentDate = date('Y-m-d');
                $partCurrentDate = explode('-', $currentDate);


                $partRetard[0] = $partCurrentDate[0] - $partRetard[0];

                $diffMois = $partCurrentDate[1] - $partRetard[1];
                if($diffMois < 0 ){
                    $partRetard[0]--;
                    $partRetard[1] = $diffMois % 12;
                } else {
                    $partRetard[1] = $diffMois;
                }
                $tabMoisJour = [31, 30, 28, 31, 30, 31, 30, 31, 30, 31, 30, 31];

                $diffJour = $partCurrentDate[2] - $partRetard[2];
                if($diffJour < 0){
                    if($partRetard[1] == 1) {
                        $partRetard[1] = 12;
                    } else {
                        $partRetard[1]--;
                        $partRetard[2] = $tabMoisJour[$partRetard[1]-1] - $diffJour;
                    }
                } else {
                    $partRetard[2] = $diffJour;
                }
                $retard = $partRetard[0].'-'.$partRetard[1].'-'.$partRetard[2];
                $where .= ' AND poi.requested_date = ' . $retard . ' AND (poi.confirmed_date IS NULL OR poi.requested_date != poi.confirmed_date)';
            }
        }

        if ($tab['prixValue'] !== null && isset($tab['prixValue'])) {
            $where .= " AND poi.price_value LIKE '%" . $tab['prixValue'] . "%'";
        }
        if ($tab['prixCode'] !== null && isset($tab['prixCode'])) {
            $where .= " AND poi.price_code LIKE '%" . $tab['prixCode'] . "%'";
        }


        if ($tab['ref'] !== null && isset($tab['ref'])) {
            $where .= " AND poi.ref LIKE '%" . $tab['ref'] . "%'";
        }

        if ($tab['supplementFret'] !== null && isset($tab['supplementFret'])) {
            $where .= " AND po.supplement_fret LIKE '%" . $tab['supplementFret'] . "%'";
        }

        if ($tab['notePo'] !== null && isset($tab['notePo'])) {
            $where .= " AND po.notes LIKE '%" . $tab['notePo'] . "%'";
        }
        if ($tab['embState'] !== null && isset($tab['embState'])) {
            $where .= " AND poi.emb_state_state LIKE '%" . $tab['embState'] . "%'";
        }

        if ($tab['refOrder'] !== null && isset($tab['refOrder'])) {
            $where .= " AND po.ref LIKE '%" . $tab['refOrder'] . "%'";
        }

        $sqlCount .= $from .$where;
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
//        dump($results);

        foreach( $results as &$element){
            $element['@context'] = '/api/contexts/PurchaseOrderItemComponent';
            $element['@type'] = 'PurchaseOrderItemComponent';
            $element['@id'] =  '/api/purchase-order-components/' . $element['id'];
            unset($element['id']);


            $item = [];
            $item['@context'] = '/api/contexts/Component';
            $item['@id'] = '/api/components/'. $element['idComp'];
            $item['type'] = 'Component';
            $item['id'] = $element['idComp'];
            $item['code'] = $element['codeComp'].'-'.$element['idComp'];
            unset($element['idComp']);
            unset($element['codeComp']);
            $element['item'] = $item;
            unset($element['component']);

            $confirmedQuantity = new Measure();
            $confirmedQuantity->setCode($element['confirmedQuantityCode']);
            $confirmedQuantity->setDenominator($element['confirmedQuantityDenominator']);
            $confirmedQuantity->setValue($element['confirmedQuantityValue']);
            $element['confirmedQuantity'] = $confirmedQuantity;
            unset($element['confirmedQuantityCode']);
            unset($element['confirmedQuantityDenominator']);
            unset($element['confirmedQuantityValue']);

            $element['targetCompany'] = '/api/companies/'. $element['targetCompany'];

            $requestedQuantity = new Measure();
            $requestedQuantity->setCode($element['requestedQuantityCode']);
            $requestedQuantity->setDenominator($element['requestedQuantityDenominator']);
            $requestedQuantity->setValue($element['requestedQuantityValue']);
            $element['requestedQuantity'] = $requestedQuantity;
            unset($element['requestedQuantityCode']);
            unset($element['requestedQuantityDenominator']);
            unset($element['requestedQuantityValue']);

            $copperPrice = new Copper();
            $priceCop =  new Measure();
            $priceCop->setCode($element['copperPriceCode']);
            $priceCop->setDenominator($element['copperPriceDenominator']);
            $priceCop->setValue($element['copperPriceValue']);
            $copperPrice->setIndex($priceCop);
            $copperPrice->setType('Currency');
            $element['copperPrice'] = $copperPrice->getIndex();
            // $element['copperPrice']['@type'] = 'Currency';
            unset($element['copperPriceCode']);
            unset($element['copperPriceDenominator']);
            unset($element['copperPriceValue']);


            $price = new Copper();
            $priceM =  new Measure();
            $priceM->setCode($element['priceCode']);
            $priceM->setDenominator($element['priceDenominator']);
            $priceM->setValue($element['priceValue']);
            $price->setIndex($priceM);
            $price->setType('Currency');
            $element['price'] = $price->getIndex();
            unset($element['priceCode']);
            unset($element['priceDenominator']);
            unset($element['priceValue']);

            $block = new Blocker();
            $block->setState($element['embBlockerState']);
            $element['embBlocker'] = $block;
            unset($element['embBlockerState']);

            $stat = new EventState();
            $stat->setState($element['embStateState']);
            $element['embState'] = $stat;
            unset($element['embStateState']);

            $order =  [];
            $order['id'] =  $element['idOrder'];
            unset($element['idOrder']);
            $order['company'] = '/api/companies/' . $element['company'];
            unset($element['company']);
            $order['contact'] = $element['contact'];
            unset($element['contact']);
            $order['deliveryCompany'] = '/api/companies/'. $element['deliveryCompany'];
            unset($element['deliveryCompany']);
            $order['notes'] = $element['notesPo'];
            unset($element['notesPo']);

            $order['supplier'] = '/api/suppliers/'.$element['supplier'];
            unset($element['supplier']);
            $idorder = '/api/selling-orders/' . $element['orderIdPo'];
            $order['parentOrder'] = $idorder;
            unset($element['orderIdPo']);
            $order['ref'] = $element['refPo'];
            unset($element['refPo']);
            $order['supplementFret'] = $element['supplementFret'] == 0 ? false : true;
            unset($element['supplementFret']);

            $stat = new EventState();
            $stat->setState($element['embStateStatepo']);
            $order['embState'] = $stat;
            unset($element['embStateStatepo']);

            $block = new Blocker();
            $block->setState($element['embBlockerStatepo']);
            $order['embBlocker'] = $block;
            unset($element['embBlockerStatepo']);

            $element['parentOrder'] = $order;
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
    public function findByComponentId(int $componentId): array
    {
        $query = $this->createQueryBuilder('ci')
            ->join('ci.item', 'c')  // Assuming 'item' is the property in ComponentItem that maps to Component
            ->where('c.id = :componentId')
            ->setParameter('componentId', $componentId)
            ->getQuery();

        return $query->getResult();
    }
}
