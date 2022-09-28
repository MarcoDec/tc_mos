<?php

namespace App\Repository;

use App\Entity\Management\Currency;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

final class CurrencyRepository extends NestedTreeRepository {
    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em, $em->getClassMetadata(Currency::class));
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
