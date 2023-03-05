<?php

declare(strict_types=1);

namespace App\Repository\Management\Unit;

use App\Entity\Management\Unit\Currency;
use Doctrine\Persistence\ManagerRegistry;

/** @extends UnitRepository<Currency> */
class CurrencyRepository extends UnitRepository {
    public function __construct(ManagerRegistry $managerRegistry, iterable $collectionExtensions, iterable $itemExtensions) {
        parent::__construct($managerRegistry, $collectionExtensions, $itemExtensions, Currency::class);
    }

    /** @param array{code: string, rate: float}[] $rates */
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
