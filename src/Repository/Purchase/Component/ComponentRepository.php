<?php

namespace App\Repository\Purchase\Component;

use App\Entity\Purchase\Component\Component;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function expires(): void {
        $this->_em->createQueryBuilder()
            ->update($this->getClassName(), 'c')
            ->set('c.embState.state', ':place')
            ->setParameter('place', 'disabled')
            ->where('c.endOfLife >= NOW()')
            ->getQuery()
            ->execute();
    }
}
