<?php

namespace App\Repository\Project\Product;

use App\Doctrine\DBAL\Types\Project\Product\CurrentPlaceType;
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
            ->set('p.currentPlace.name', ':place')
            ->setParameter('place', CurrentPlaceType::TYPE_DISABLED)
            ->where('p.expirationDate >= NOW()')
            ->getQuery()
            ->execute();
    }
}
