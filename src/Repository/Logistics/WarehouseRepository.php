<?php

namespace App\Repository\Logistics;

use App\Doctrine\DBAL\Types\Logistics\FamilyType;
use App\Entity\Logistics\Warehouse;
use App\Security\SecurityTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

/**
 * @extends ServiceEntityRepository<Warehouse>
 *
 * @method null|Warehouse find($id, $lockMode = null, $lockVersion = null)
 * @method null|Warehouse findOneBy(array $criteria, ?array $orderBy = null)
 * @method Warehouse[]    findAll()
 * @method Warehouse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class WarehouseRepository extends ServiceEntityRepository {
    use SecurityTrait {
        __construct as private constructSecurity;
    }

    public function __construct(ManagerRegistry $registry, Security $security) {
        parent::__construct($registry, Warehouse::class);
        $this->constructSecurity($security);
    }

    /**
     * @return Warehouse[]
     */
    public function findImport(): array {
        /** @phpstan-ignore-next-line */
        return $this->createQueryBuilder('w')
            ->where('w.deleted = FALSE')
            ->andWhere('((w.company = :company AND w.families LIKE \'%'.FamilyType::TYPE_RECEIPT.'%\') OR (w.destination = :company AND w.families LIKE \'%'.FamilyType::TYPE_TRUCK.'%\'))')
            ->setParameter('company', $this->getCompanyId())
            ->getQuery()
            ->getResult();
    }
}
