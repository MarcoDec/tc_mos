<?php

namespace App\Repository;

use App\Entity\Management\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Currency>
 *
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Currency[]    findAll()
 * @method Currency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class CurrencyRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Currency::class);
    }

    /**
     * @param array{code: string, rate: float}[] $rates
     */
    public function updateRates(array $rates): void {
        foreach ($rates as $rate) {
            $this->_em->createQueryBuilder()
                ->update($this->getClassName(), 'c')
                ->set('c.base', ':rate')
                ->where('c.code = :code')
                ->setParameters(['code' => $rate['code'], 'rate' => $rate['rate']])
                ->getQuery()
                ->execute();
        }
    }
}
