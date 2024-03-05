<?php

namespace App\Repository;

use App\Entity\Management\Currency;
use App\Entity\Management\Unit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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

    /**
     * @param string[] $loaded
     */
    private static function loadCurrency(Currency $currency, array &$loaded): void {
        if (in_array($currency->getCode(), $loaded)) {
            return;
        }

        $loaded[] = $currency->getCode();
        $children = $currency->getChildren();
        foreach ($children as $child) {
            self::loadCurrency($child, $loaded);
        }
        $parent = $currency->getParent();
        if (!empty($parent)) {
            self::loadCurrency($parent, $loaded);
        }
    }

    /**
     * @return Currency[]
     */
    public function loadAll(): array {
        /** @var Currency[] $items */
        $items = $this->createQueryBuilder('u', 'u.code')
            ->addSelect('c')
            ->addSelect('p')
            ->leftJoin('u.children', 'c', Join::WITH, 'c.deleted = FALSE')
            ->leftJoin('u.parent', 'p', Join::WITH, 'p.deleted = FALSE')
            ->where('u.deleted = FALSE')
            ->getQuery()
            ->getResult();
        $loaded = [];
        foreach ($items as $item) {
            self::loadCurrency($item, $loaded);
        }
        return $items;
    }
}
