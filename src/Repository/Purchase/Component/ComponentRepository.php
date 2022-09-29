<?php

namespace App\Repository\Purchase\Component;

use App\Entity\Purchase\Component\Component;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Component>
 *
 * @method Component|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Component[]    findAll()
 * @method Component[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ComponentRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Component::class);
    }

    public function expires(): void {
        $this->_em->createQueryBuilder()
            ->update($this->getClassName(), 'c')
            ->set('c.embState.state', ':place')
            ->setParameter('place', 'disabled')
            ->where('c.endOfLife >= NOW()')
            ->getQuery()
            ->execute();
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?Component {
        $query = $this->createQueryBuilder('c')
            ->addSelect('u')
            ->leftJoin('c.unit', 'u', Join::WITH, 'u.deleted = FALSE')
            ->where('c.deleted = FALSE')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
        try {
            /** @phpstan-ignore-next-line */
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }
    }
}
