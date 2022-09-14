<?php

namespace App\Repository\Project\Product;

use App\Entity\Project\Product\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method null|Product find($id, $lockMode = null, $lockVersion = null)
 * @method null|Product findOneBy(array $criteria, ?array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ProductRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Product::class);
    }

    public function expires(): void {
        $this->_em->createQueryBuilder()
            ->update($this->getClassName(), 'p')
            ->set('p.embState.state', ':place')
            ->setParameter('place', 'disabled')
            ->where('p.endOfLife >= NOW()')
            ->getQuery()
            ->execute();
    }
}
