<?php

namespace App\Repository\Management;

use App\Entity\Management\InvoiceTimeDue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InvoiceTimeDue>
 *
 * @method InvoiceTimeDue|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceTimeDue|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method InvoiceTimeDue[]    findAll()
 * @method InvoiceTimeDue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class InvoiceTimeDueRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, InvoiceTimeDue::class);
    }

    public function unify(): void {
        $groups = collect($this->findAll())->groupBy->getName();
        $doubles = [];
        foreach ($groups as $times) {
            if (count($times) === 2 && $times[1] !== null) {
                $doubles[] = $times[1]->getId();
            }
        }
        $this->_em->createQueryBuilder()
            ->delete($this->getClassName(), 'i')
            ->where('i.id IN (:doubles)')
            ->setParameter('doubles', $doubles)
            ->getQuery()
            ->execute();
    }
}
