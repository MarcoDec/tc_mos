<?php

namespace App\Repository\Hr\Employee;

use App\Entity\Hr\Employee\Employee;
use App\Entity\Hr\Employee\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;
use UnexpectedValueException;

/**
 * @extends ServiceEntityRepository<Notification>
 *
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class NotificationRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry, private Security $security) {
        parent::__construct($registry, Notification::class);
    }

    public function delete(): void {
        $this->createReadQueryBuilder()->set('n.deleted', true)->getQuery()->execute();
    }

    /**
     * @return Notification[]
     */
    public function read(): array {
        $this->createReadQueryBuilder()->getQuery()->execute();
        return $this->findBy(['user' => $this->getUser()]);
    }

    private function createReadQueryBuilder(): QueryBuilder {
        return $this->_em->createQueryBuilder()
            ->update($this->getClassName(), 'n')
            ->set('n.read', true)
            ->where('n.user = :user')
            ->setParameter('user', $this->getUser());
    }

    private function getUser(): int {
        $user = $this->security->getUser();
        if (!($user instanceof Employee)) {
            throw new UnexpectedValueException(sprintf('Expected argument of type "%s", "%s" given.', Employee::class, get_debug_type($user)));
        }
        $id = $user->getId();
        if (empty($id)) {
            throw new UnexpectedValueException(sprintf('Expected argument of type int, "%s" given.', get_debug_type($user)));
        }
        return $id;
    }
}
