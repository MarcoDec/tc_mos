<?php

declare(strict_types=1);

namespace App\Repository\Hr\Employee;

use App\Entity\Hr\Employee\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/** @extends ServiceEntityRepository<Employee> */
class EmployeeRepository extends ServiceEntityRepository implements UserLoaderInterface, UserProviderInterface {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Employee::class);
    }

    public function loadUserByIdentifier(string $identifier): Employee {
        $query = $this->createQueryBuilder('e')
            ->where('e.deleted = FALSE')
            ->andWhere('e.username = :username')
            ->setParameter('username', $identifier)
            ->getQuery();
        try {
            $user = $query->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            $user = null;
        }
        /** @var Employee|null $user */
        if (empty($user)) {
            $e = new UserNotFoundException();
            $e->setUserIdentifier($identifier);
            throw $e;
        }
        return $user;
    }

    public function refreshUser(UserInterface $user): Employee {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool {
        return $class === $this->getClassName();
    }
}
