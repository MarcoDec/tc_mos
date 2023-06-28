<?php

namespace App\Repository\Selling\Order;

use App\Entity\Selling\Order\ProductItem;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ItemRepository<ProductItem>
 *
 * @method null|ProductItem find($id, $lockMode = null, $lockVersion = null)
 * @method null|ProductItem findOneBy(array $criteria, ?array $orderBy = null)
 * @method ProductItem[]    findAll()
 * @method ProductItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ProductItemRepository extends ItemRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ProductItem::class);
    }

    public function createPatchQueryBuilder(int $id): QueryBuilder {
        return parent::createPatchQueryBuilder($id)->addSelect('p')->innerJoin('i.item', 'p');
    }

    public function findOneByPatch(int $id): ?ProductItem {
        $query = $this->createPatchQueryBuilder($id)->getQuery();
        try {
            /** @phpstan-ignore-next-line */
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
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

    public function findBySupplierId($productId, $tab = null) : array
    {

        $currentPage = $tab['currentPage'];
        $first = 0;
        $max = 15;
    
        if ($currentPage !== null && $currentPage !== '1'){
            $first = 15 * $currentPage -1;
            $max = $first + 15;
        }

        $query =  $this->createQueryBuilder('i')
            ->select('i')
            ->from(Order::class, 'o')
            ->where('i.order = o')
            ->andWhere('o.product = :product')
            ->andWhere('i.deleted = FALSE')
            ->andWhere('o.deleted = FALSE')
            ->setParameter('product', $productId);

        if ($tab['item'] !== null) {
            $nbItem = explode('/', $tab['item']);
            $nbItem = $nbItem[3];
            $query->andWhere('i.item = :composant')
                ->setParameter('composant',$nbItem);
        }
        if ($tab['confQuantityCode'] !== null) {
            $query->andWhere('i.confirmedQuantity.code LIKE :confQuantityCode')
                ->setParameter('confQuantityCode', '%' . $tab['confQuantityCode'] . '%');
        }
        if ($tab['confQuantityValue'] !== null) {
            $query->andWhere('i.confirmedQuantity.value LIKE :confQuantityValue')
                ->setParameter('confQuantityValue', '%' . $tab['confQuantityValue'] . '%');
        }
        if ($tab['reqQuantityCode'] !== null) {
            $query->andWhere('i.requestedQuantity.code LIKE :reqQuantityCode')
                ->setParameter('reqQuantityCode', '%' . $tab['reqQuantityCode'] . '%');
        }
        if ($tab['reqQuantityValue'] !== null) {
            $query->andWhere('i.requestedQuantity.value LIKE :reqQuantityValue')
                ->setParameter('reqQuantityValue', '%' . $tab['reqQuantityValue'] . '%');
        }
        if ($tab['confDate'] !== null) {
            $query->andWhere('i.confirmedDate LIKE :confDate')
                ->setParameter('confDate', '%' . $tab['confDate'] . '%');
        }
        if ($tab['reqDate'] !== null) {
            $query->andWhere('i.requestedDate LIKE :reqDate')
                ->setParameter('reqDate', '%' . $tab['reqDate'] . '%');
        }
        if ($tab['note'] !== null) {
            $query->andWhere('i.notes LIKE :note')
                ->setParameter('note', '%' . $tab['note'] . '%');
        }

        if ($tab['retard'] !== null) {
            if (strcasecmp($tab['retard'], 'aucun') === 0 || $tab['retard'] === '0') {
        
                $query->andWhere('i.requestedDate = i.confirmedDate');
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

                $query->andWhere('i.requestedDate = :retard')
                    ->andWhere('i.confirmedDate IS NULL OR i.requestedDate != i.confirmedDate')
                    ->setParameter('retard', $retard);
            }
        }

        if ($tab['prixValue'] !== null) {
            $query->andWhere('i.price.value LIKE :prixValue')
                ->setParameter('prixValue', '%' . $tab['prixValue'] . '%');
        }
        if ($tab['prixCode'] !== null) {
            $query->andWhere('i.price.code LIKE :prixCode')
                ->setParameter('prixCode', '%' . $tab['prixCode'] . '%');
        }

        
        if ($tab['ref'] !== null) {
            $query->andWhere('i.notes LIKE :ref')
                ->setParameter('ref', '%' . $tab['ref'] . '%');
        }

        $query->setFirstResult($first)
            ->setMaxResults($max);
        
        return ($query->getQuery()->getResult());
    }
}
