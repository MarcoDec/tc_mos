<?php

namespace App\Repository\Hr\Employee;

use App\Entity\Api\Token;
use App\Entity\Hr\Employee\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @extends ServiceEntityRepository<Employee>
 *
 * @method Employee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employee|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Employee[]    findAll()
 * @method Employee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EmployeeRepository extends ServiceEntityRepository implements UserProviderInterface {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Employee::class);
    }

    public function findByToken(string $token): ?Employee {
        return !empty($apiToken = $this->_em->getRepository(Token::class)->findOneBy(['token' => $token]))
            ? $apiToken->getEmployee()
            : null;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface {
        if (!empty($user = $this->findOneBy(['username' => $identifier]))) {
            return $user;
        }
        throw new UserNotFoundException();
    }

    public function loadUserByUsername(string $username): UserInterface {
        return $this->loadUserByIdentifier($username);
    }

    public function refreshUser(UserInterface $user): UserInterface {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool {
        return $class === Employee::class;
    }
}
