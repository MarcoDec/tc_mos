<?php

namespace App\Repository\Purchase\Order;

use App\Doctrine\DBAL\Connection;
use App\Entity\Purchase\Order\ComponentItem;
use App\Entity\Purchase\Order\Item;
use App\Entity\Purchase\Order\ProductItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Illuminate\Support\Collection;

/**
 * @template I of Item
 *
 * @extends ServiceEntityRepository<I>
 *
 * @phpstan-type ItemFilter array{'embState.state'?: string[], order?: \App\Entity\Purchase\Order\Order}
 *
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Item[]    findAll()
 */
class ItemRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry, string $entityClass = Item::class) {
        parent::__construct($registry, $entityClass);
    }

    /**
     * @param ItemFilter                  $criteria
     * @param array<string, 'asc'|'desc'> $orderBy
     */
    public function createByQueryBuilder(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): QueryBuilder {
        $qb = $this->createQueryBuilder('i')
            ->addSelect('o')
            ->addSelect('r')
            ->leftJoin('i.order', 'o')
            ->leftJoin('i.receipts', 'r')
            ->setMaxResults($limit)
            ->setFirstResult($offset);
        if (isset($criteria['embState.state'])) {
            $qb
                ->andWhere('i.embState.state IN (:state)')
                ->setParameter('state', $criteria['embState.state'], Connection::PARAM_STR_ARRAY);
        }
        if (isset($criteria['order'])) {
            $qb->andWhere('i.order = :order')->setParameter('order', $criteria['order']->getId());
        }
        return $qb;
    }

    public function createReceiptQueryBuilder(int $id): QueryBuilder {
        return $this->createQueryBuilder('i')
            ->addSelect('c')
            ->addSelect('company')
            ->addSelect('company_references')
            ->addSelect('o')
            ->addSelect('r')
            ->addSelect('ref')
            ->addSelect('s')
            ->addSelect('s_references')
            ->leftJoin('i.order', 'o')
            ->leftJoin('o.company', 'company')
            ->leftJoin('company.references', 'company_references')
            ->leftJoin('o.supplier', 's')
            ->leftJoin('s.references', 's_references')
            ->leftJoin('i.receipts', 'r')
            ->leftJoin('r.checks', 'c')
            ->leftJoin('c.reference', 'ref')
            ->where('i.id = :id')
            ->setParameter('id', $id);
    }

    /**
     * @param ItemFilter $criteria
     *
     * @return (ComponentItem|ProductItem)[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        /** @var Collection<int, ComponentItem|ProductItem> $items */
        $items = collect($this->_em->getRepository(ComponentItem::class)->findBy($criteria, $orderBy, $limit, $offset));
        return $items->merge($this->_em->getRepository(ProductItem::class)->findBy($criteria, $orderBy, $limit, $offset))
            ->values()
            ->all();
    }

    public function findOneByReceipt(int $id): ComponentItem|ProductItem|null {
        return $this->_em->getRepository(ComponentItem::class)->findOneByReceipt($id)
            ?? $this->_em->getRepository(ProductItem::class)->findOneByReceipt($id);
    }
}
