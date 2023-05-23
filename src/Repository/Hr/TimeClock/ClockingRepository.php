<?php

namespace App\Repository\Hr\TimeClock;

use App\Entity\Hr\Employee\Employee;
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

   public function getPreviousClocking(Employee $employee): ?Clocking {
      $employeeClockings = $this->findBy(['employee'=>$employee, 'deleted'=>false]);
      if (count($employeeClockings)==0) {
         return null;
      }
      usort($employeeClockings, function (Clocking $a, Clocking $b){
         return $a->getDate()<$b->getDate();
      });
      return $employeeClockings[0];

   }
}
