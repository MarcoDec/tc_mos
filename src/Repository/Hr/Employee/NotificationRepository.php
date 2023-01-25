<?php

namespace App\Repository\Hr\Employee;

use App\Entity\Hr\Employee\Notification;
use App\Security\SecurityTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<Notification>
 *
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class NotificationRepository extends ServiceEntityRepository {
    use SecurityTrait {
        __construct as private constructSecurity;
    }

    public function __construct(ManagerRegistry $registry, Security $security) {
        parent::__construct($registry, Notification::class);
        $this->constructSecurity($security);
    }

    public function delete(string $category): void {
        $this->createReadQueryBuilder($category)->set('n.deleted', true)->getQuery()->execute();
    }

    /**
     * @return Notification[]
     */
    public function read(string $category): array {
        $this->createReadQueryBuilder($category)->getQuery()->execute();
        return $this->findBy(['user' => $this->getUserId()]);
    }

    private function createReadQueryBuilder(string $category): QueryBuilder {
        return $this->_em->createQueryBuilder()
            ->update($this->getClassName(), 'n')
            ->set('n.read', true)
            ->where('n.user = :user')
            ->andWhere('n.category = :category')
            ->setParameters(['category' => $category, 'user' => $this->getUserId()]);
    }
}
