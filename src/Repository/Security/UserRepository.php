<?php

namespace App\Repository\Security;

use App\Entity\Hr\Employee\Employee;
use App\Entity\Security\User;
use App\Repository\Hr\Employee\EmployeeRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function get_class;
use LogicException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
final class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserProviderInterface {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, User::class);
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?User {
        return $this->getEmployeeRepository()->find($id, $lockMode, $lockVersion);
    }

    /**
     * @return User[]
     */
    public function findAll(): array {
        return $this->getEmployeeRepository()->findAll();
    }

    /**
     * @return User[]
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
        return $this->getEmployeeRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function findOneBy(array $criteria, ?array $orderBy = null): ?User {
        return $this->getEmployeeRepository()->findOneBy($criteria, $orderBy);
    }

    public function loadUserByIdentifier(string $identifier): ?User {
        return $this->findOneBy(['username' => $identifier]);
    }

    public function loadUserByUsername(string $username): ?User {
        return $this->loadUserByIdentifier($username);
    }

    public function refreshUser(UserInterface $user): ?User {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool {
        return $class === User::class || is_subclass_of($class, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    private function getEmployeeRepository(): EmployeeRepository {
        if (($repo = $this->getEntityManager()->getRepository(Employee::class)) instanceof EmployeeRepository) {
            return $repo;
        }
        throw new LogicException(sprintf('Unexpected value. Expect %s, get %s.', EmployeeRepository::class, get_class($repo)));
    }
}
