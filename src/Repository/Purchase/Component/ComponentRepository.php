<?php

namespace App\Repository\Purchase\Component;

use App\Doctrine\DBAL\Types\Embeddable\BlockerStateType;
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
            ->set('c.embBlocker.state', ':place')
            ->setParameter('place', BlockerStateType::TYPE_STATE_DISABLED)
            ->where('c.endOfLife >= NOW()')
            ->getQuery()
            ->execute();
    }

    public function find($id, $lockMode = null, $lockVersion = null): ?Component {
        $query = $this->createQueryBuilder('c')
            ->addSelect('u')
            ->leftJoin('c.unit', 'u', Join::WITH, 'u.deleted = FALSE')
            ->leftJoin( 'c.family', 'f', Join::WITH, 'f.deleted = FALSE')
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

    /** @return Component[] */
    public function findOptions(): array {
        /** @phpstan-ignore-next-line */
        return $this->createQueryBuilder('c')
            ->select('partial c.{id}')
            ->addSelect('partial f.{code, id}')
            ->innerJoin('c.family', 'f', Join::WITH, 'f.deleted = FALSE')
            ->where('c.deleted = FALSE')
            ->orderBy('c.id')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find a Component by ID with related entities (Unit and Family).
     *
     * @param int $id
     *
     * @return Component|null
     */
    public function findById(int $id): ?Component
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->addSelect('u') // Add the related entities to the select
            ->leftJoin('c.unit', 'u', Join::WITH, 'u.deleted = FALSE')
            ->where('c.deleted = FALSE')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id);
    
        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            return null;
        }
    }


}
