<?php

namespace App\Repository\Management;

use App\Entity\Management\Printer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Printer>
 *
 * @method null|Printer find($id, $lockMode = null, $lockVersion = null)
 * @method null|Printer findOneBy(array $criteria, ?array $orderBy = null)
 * @method Printer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PrinterRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Printer::class);
    }

    /**
     * @return Printer[]
     */
    public function findAll(): array {
        /** @phpstan-ignore-next-line */
        return $this->createQueryBuilder('p')
            ->addSelect('c')
            ->innerJoin('p.company', 'c', Join::WITH, 'c.deleted = FALSE')
            ->getQuery()
            ->getResult();
    }
}
