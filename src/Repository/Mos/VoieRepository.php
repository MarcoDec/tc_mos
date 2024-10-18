<?php

namespace App\Repository\Mos;

use App\Entity\Mos\Voie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Voie>
 *
 * @method null|Voie find($id, $lockMode = null, $lockVersion = null)
 * @method null|Voie findOneBy(array $criteria, array $orderBy = null)
 * @method Voie[]    findAll()
 * @method Voie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoieRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Voie::class);
    }

//    /**
//     * @return Voie[] Returns an array of Voie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Voie
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
