<?php

namespace App\Repository\Mos;

use App\Entity\Mos\Accessoire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Accessoire>
 *
 * @method Accessoire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Accessoire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Accessoire[]    findAll()
 * @method Accessoire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessoireRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Accessoire::class);
    }

//    /**
//     * @return Accessoire[] Returns an array of Accessoire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Accessoire
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
