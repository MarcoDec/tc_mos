<?php

namespace App\Repository\Logistics\Stock;

use App\Entity\Logistics\Stock\ComponentStock;
use App\Entity\Logistics\Stock\ProductStock;
use App\Entity\Logistics\Stock\Stock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template T of Stock
 *
 * @extends ServiceEntityRepository<T>
 *
 * @method null|T find($id, $lockMode = null, $lockVersion = null)
 * @method null|T findOneBy(array $criteria, ?array $orderBy = null)
 * @method T[]    findAll()
 * @method T[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StockRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry, string $entityClass = Stock::class) {
        parent::__construct($registry, $entityClass);
    }

    /** @return null|T */
    final public function findPatch(int $id): ?Stock {
        /** @phpstan-ignore-next-line */
        return $this->_em->getRepository(ComponentStock::class)->loadPatch($id)
            ?? $this->_em->getRepository(ProductStock::class)->loadPatch($id);
    }

    /** @return null|T */
    private function loadPatch(int $id): ?Stock {
        $query = $this
            ->createQueryBuilder('s')
            ->addSelect('i')
            ->addSelect('u')
            ->innerJoin('s.item', 'i')
            ->innerJoin('i.unit', 'u')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
        try {
            $stock = $query->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            $stock = null;
        }
        /** @var null|T $stock */
        if (empty($stock)) {
            return $stock;
        }
        $receipts = $stock->getReceipts();
        if (method_exists($receipts, 'setInitialized')) {
            $receipts->setInitialized(true);
        }
        return $stock;
    }
     
    /**
     * @return Stock[]|null
     */
    public function findStocksByCriteria(): ?array
    {
        $queryBuilder = $this->createQueryBuilder('s');

        // Ajouter les conditions spécifiées
        $queryBuilder
            ->andWhere('s.jail = :jail')
            ->andWhere('s INSTANCE OF App\Entity\Logistics\Stock\ComponentStock')
            ->andWhere('s.quantity.value > 0')
            ->setParameter('jail', 0);

        // Exécuter la requête
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }
}
