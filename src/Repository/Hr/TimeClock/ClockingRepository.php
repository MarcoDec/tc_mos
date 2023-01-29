<?php

namespace App\Repository\Hr\TimeClock;

use App\Entity\Hr\TimeClock\Clocking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Clocking>
 *
 * @method Clocking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Clocking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Clocking[]    findAll()
 * @method Clocking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ClockingRepository extends ServiceEntityRepository {
   public function __construct(ManagerRegistry $registry) {
      parent::__construct($registry, Clocking::class);
   }

   public function supportsClass(string $class): bool {
      return $class === $this->getClassName();
   }
}
