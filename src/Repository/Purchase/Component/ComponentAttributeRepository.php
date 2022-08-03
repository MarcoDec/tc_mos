<?php

namespace App\Repository\Purchase\Component;

use App\Entity\Purchase\Component\ComponentAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ComponentAttribute>
 *
 * @method ComponentAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComponentAttribute|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method ComponentAttribute[]    findAll()
 * @method ComponentAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class ComponentAttributeRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ComponentAttribute::class);
    }

    public function links(): void {
        $this->_em->getConnection()->executeQuery('CALL LINK_COMPONENTS_ATTRIBUTES');
    }
}
