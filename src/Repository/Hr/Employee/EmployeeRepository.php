<?php

namespace App\Repository\Hr\Employee;

use App\Entity\Hr\Employee\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Proxy\Proxy;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @extends ServiceEntityRepository<Employee>
 *
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EmployeeRepository extends ServiceEntityRepository implements UserLoaderInterface, UserProviderInterface {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Employee::class);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface {
        $query = $this->createQueryBuilder('e')
            ->select('partial e.{company, embBlocker.state, embRoles.roles, embState.state, id, initials, name, password, surname, username}')
            ->addSelect('t')
            ->leftJoin('e.apiTokens', 't')
            ->where('e.username = :username')
            ->setParameter('username', $identifier)
            ->getQuery();
        try {
            $user = $query->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            $user = null;
        }
        /** @var Employee|null $user */
        if (empty($user)) {
            $e = new UserNotFoundException(sprintf('User "%s" not found.', $identifier));
            $e->setUserIdentifier($identifier);
            throw $e;
        }
        if (($company = $user->getCompany()) instanceof Proxy) {
            $company->__setInitialized(true);
        }
        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool {
        return $class === $this->getClassName();
    }
}
