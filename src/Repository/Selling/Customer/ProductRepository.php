<?php

namespace App\Repository\Selling\Customer;

use App\Entity\Selling\Customer\Price\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method null|Product find($id, $lockMode = null, $lockVersion = null)
 * @method null|Product findOneBy(array $criteria, ?array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Product::class);
    }

    public function findWithUnit(int $id): ?Product {
        $query = $this
            ->createQueryBuilder('cp')
            ->addSelect('p')
            ->addSelect('u')
            ->innerJoin('cp.product', 'p')
            ->innerJoin('p.unit', 'u')
            ->where('cp.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
        try {
            /** @phpstan-ignore-next-line */
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    public function findByCustomerIdSocieties($customerId): array
    {
        return $this->createQueryBuilder('p')
            ->join('p.customer', 'c')
            ->join('c.society', 's') 
            ->andWhere('c.id = :customerId')
            ->setParameter('customerId', $customerId)
            ->select('DISTINCT s.id ') 
            ->getQuery()
            ->getResult();
    }

}
