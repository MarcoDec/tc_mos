<?php

namespace App\Repository\Selling\Customer;

use App\Entity\Selling\Customer\Price\Component;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Component>
 *
 * @method null|Component find($id, $lockMode = null, $lockVersion = null)
 * @method null|Component findOneBy(array $criteria, ?array $orderBy = null)
 * @method Component[]    findAll()
 * @method Component[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComponentRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Component::class);
    }

    public function findWithUnit(int $id): ?Component {
        $query = $this
            ->createQueryBuilder('cp')
            ->addSelect('p')
            ->addSelect('u')
            ->innerJoin('cp.component', 'p')
            ->innerJoin('p.unit', 'u')
            ->where('cp.id = :id')
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