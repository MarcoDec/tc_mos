<?php

namespace App\Repository;

use App\Entity\CronJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CronJob>
 *
 * @method CronJob|null find($id, $lockMode = null, $lockVersion = null)
 * @method CronJob|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method CronJob[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CronJobRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, CronJob::class);
    }

    /**
     * @return CronJob[]
     */
    public function findAll(): array {
        return $this->createQueryBuilder('j', 'j.command')->getQuery()->getResult();
    }
}
