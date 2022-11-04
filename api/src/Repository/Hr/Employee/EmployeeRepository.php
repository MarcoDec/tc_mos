<?php

declare(strict_types=1);

namespace App\Repository\Hr\Employee;

use App\Entity\Hr\Employee\Employee;
use App\Entity\Token;
use App\Repository\TokenRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
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
        $this->_em->beginTransaction();
        try {
            $user = $this->findUser($identifier);
            $this->getTokenRepo()->connect($user);
        } catch (Exception $exception) {
            $this->_em->rollback();
            throw $exception;
        }
        $this->_em->commit();
        return $user;
    }

    public function refreshUser(UserInterface $user): Employee {
        $this->_em->beginTransaction();
        try {
            $user = $this->findUser($user->getUserIdentifier());
            $this->getTokenRepo()->renew($user);
        } catch (Exception $exception) {
            $this->_em->rollback();
            throw $exception;
        }
        $this->_em->commit();
        return $user;
    }

    public function supportsClass(string $class): bool {
        return $class === $this->getClassName();
    }

    private function findUser(string $identifier): Employee {
        $query = $this->createQueryBuilder('e')
            ->addSelect('t')
            ->leftJoin('e.apiTokens', 't', Join::WITH, 't.deleted = FALSE')
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

    private function getTokenRepo(): TokenRepository {
        return $this->_em->getRepository(Token::class);
    }
}
