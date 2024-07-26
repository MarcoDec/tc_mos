<?php

namespace App\Repository\Project\Product;

use App\Entity\Project\Product\Nomenclature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Nomenclature>
 *
 * @method null|Nomenclature find($id, $lockMode = null, $lockVersion = null)
 * @method null|Nomenclature findOneBy(array $criteria, ?array $orderBy = null)
 * @method Nomenclature[]    findAll()
 * @method Nomenclature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class NomenclatureRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Nomenclature::class);
    }
    
/**
 * Trouve les nomenclatures par ID du produit
 *
 * @param int $productId
 * @return Nomenclature[]|null
 */
public function findByProductId(int $productId): ?array
{
    return $this->createQueryBuilder('n')
        ->andWhere('n.product = :productId')
        ->setParameter('productId', $productId)
        ->getQuery()
        ->getResult();
}
}