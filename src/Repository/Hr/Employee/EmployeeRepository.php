<?php

namespace App\Repository\Hr\Employee;

use App\Entity\Hr\Employee\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Employee>
 *
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EmployeeRepository extends ServiceEntityRepository implements UserLoaderInterface {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Employee::class);
    }

    public function loadUserByIdentifier(string $identifier): ?UserInterface {
        $query = $this->createQueryBuilder('e')
            ->addSelect('c')
            ->addSelect('s')
            ->addSelect('t')
            ->leftJoin('e.apiTokens', 't')
            ->leftJoin('e.company', 'c')
            ->leftJoin('c.society', 's')
            ->where('e.username = :username')
            ->setParameter('username', $identifier)
            ->getQuery();
        try {
            /** @phpstan-ignore-next-line */
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }
    }
}
