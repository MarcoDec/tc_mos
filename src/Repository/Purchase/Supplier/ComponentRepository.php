<?php

namespace App\Repository\Purchase\Supplier;

use App\Entity\Purchase\Supplier\Component;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Component>
 *
 * @method Component|null find($id, $lockMode = null, $lockVersion = null)
 * @method Component|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Component[]    findAll()
 * @method Component[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ComponentRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Component::class);
    }

    public function findWithUnit(int $id): ?Component {
        $query = $this
            ->createQueryBuilder('cs')
            ->addSelect('c')
            ->addSelect('u')
            ->innerJoin('cs.component', 'c')
            ->innerJoin('c.unit', 'u')
            ->where('cs.id = :id')
            ->setParameter('id', $id)
            ->getQuery();
        try {
            /** @phpstan-ignore-next-line */
            return $query->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
