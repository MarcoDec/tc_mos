<?php

declare(strict_types=1);

namespace App\Repository;

use App\Doctrine\ORM\QueryBuilderTrait;
use App\Entity\CronJob;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<CronJob> */
class CronJobRepository extends ServiceEntityRepository {
    use QueryBuilderTrait;

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, CronJob::class);
    }

    /** @return CronJob[] */
    public function findAll(): array {
        $jobs = $this->createQueryBuilder('j', 'j.command')->getQuery()->getResult();
        return is_array($jobs) ? $jobs : [];
    }
}
