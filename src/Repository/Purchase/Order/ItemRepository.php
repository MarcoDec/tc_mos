<?php

namespace App\Repository\Purchase\Order;

use App\Collection;
use App\Doctrine\DBAL\Connection;
use App\Entity\Purchase\Order\ComponentItem;
use App\Entity\Purchase\Order\Item;
use App\Entity\Purchase\Order\ProductItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template I of Item
 * @extends ServiceEntityRepository<I>
 * @method I|null find($id, $lockMode = null, $lockVersion = null)
 * @method I|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method I[]    findAll()
 */
class ItemRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry, string $entityClass = Item::class) {
        parent::__construct($registry, $entityClass);
    }

    /**
     * @param array $criteria
     * @param array<string, 'asc'|'desc'> $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return QueryBuilder
     */
    public function createByQueryBuilder(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): QueryBuilder {
        $qb = $this->createQueryBuilder('i')
            ->addSelect('o')
            ->addSelect('r')
            ->leftJoin('i.parentOrder', 'o', Join::WITH, 'o.deleted = FALSE')
            ->leftJoin('i.receipts', 'r', Join::WITH, 'r.deleted = FALSE')
            ->where('i.deleted = FALSE')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        if (isset($criteria['embState.state'])) {
            $qb
                ->andWhere('i.embState.state IN (:state)')
                ->setParameter('state', $criteria['embState.state'], Connection::PARAM_STR_ARRAY);
        }
        if (isset($criteria['parentOrder'])) {
            $qb->andWhere('i.parentOrder = :parentOrder')->setParameter('parentOrder', $criteria['parentOrder']->getId());
        }
        return $qb;
    }

    public function createCheckQueryBuilder(int $id): QueryBuilder {
        return $this->createQueryBuilder('i')
            ->addSelect('c')
            ->addSelect('company')
            ->addSelect('company_references')
            ->addSelect('o')
            ->addSelect('r')
            ->addSelect('ref')
            ->addSelect('s')
            ->addSelect('s_references')
            ->leftJoin('i.parentOrder', 'o', Join::WITH, 'o.deleted = FALSE')
            ->leftJoin('o.company', 'company', Join::WITH, 'company.deleted = FALSE')
            ->leftJoin('company.references', 'company_references', Join::WITH, 'company_references.deleted = FALSE')
            ->leftJoin('o.supplier', 's', Join::WITH, 's.deleted = FALSE')
            ->leftJoin('s.references', 's_references', Join::WITH, 's_references.deleted = FALSE')
            ->leftJoin('i.receipts', 'r', Join::WITH, 'r.deleted = FALSE')
            ->leftJoin('r.checks', 'c', Join::WITH, 'c.deleted = FALSE')
            ->leftJoin('c.reference', 'ref', Join::WITH, 'ref.deleted = FALSE')
            ->where('i.deleted = FALSE')
            ->andWhere('i.id = :id')
            ->setParameter('id', $id);
    }

    public function createReceiptQueryBuilder(int $id): QueryBuilder {
        return $this->createQueryBuilder('i')
            ->addSelect('o')
            ->addSelect('r')
            ->leftJoin('i.parentOrder', 'o', Join::WITH, 'o.deleted = FALSE')
            ->leftJoin('i.receipts', 'r', Join::WITH, 'r.deleted = FALSE')
            ->where('i.deleted = FALSE')
            ->andWhere('i.id = :id')
            ->setParameter('id', $id);
    }

    /**
     * @param ItemFilter $criteria
     *
     * @return (ComponentItem|ProductItem)[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        return Collection::collect($this->_em->getRepository(ComponentItem::class)->findBy($criteria, $orderBy, $limit, $offset))
            ->merge($this->_em->getRepository(ProductItem::class)->findBy($criteria, $orderBy, $limit, $offset))
            ->all();
    }

    public function findOneByCheck(int $id): ComponentItem|ProductItem|null {
        return $this->_em->getRepository(ComponentItem::class)->findOneByCheck($id)
            ?? $this->_em->getRepository(ProductItem::class)->findOneByCheck($id);
    }

    public function findOneByReceipt(int $id, string $resourceClass): ComponentItem|ProductItem|null {
        return $this->_em->getRepository(ComponentItem::class)->findOneByReceipt($id,$resourceClass)
            ?? $this->_em->getRepository(ProductItem::class)->findOneByReceipt($id,$resourceClass);
    }
}
