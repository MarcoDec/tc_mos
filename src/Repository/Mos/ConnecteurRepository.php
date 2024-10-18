<?php

namespace App\Repository\Mos;

use App\Entity\Mos\Connecteur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Connecteur>
 *
 * @method Connecteur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Connecteur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Connecteur[]    findAll()
 * @method Connecteur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConnecteurRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Connecteur::class);
    }

//    /**
//     * @return Connecteur[] Returns an array of Connecteur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Connecteur
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
