<?php

namespace App\Repository\Management;

use App\Entity\Management\Unit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Unit>
 *
 * @method null|Unit find($id, $lockMode = null, $lockVersion = null)
 * @method null|Unit findOneBy(array $criteria, ?array $orderBy = null)
 * @method Unit[]    findAll()
 * @method Unit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class UnitRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Unit::class);
    }

    /**
     * @param string[] $loaded
     */
    private static function loadUnit(Unit $unit, array &$loaded): void {
        if (in_array($unit->getCode(), $loaded)) {
            return;
        }

        $loaded[] = $unit->getCode();
        $children = $unit->getChildren();
        foreach ($children as $child) {
            self::loadUnit($child, $loaded);
        }
        $parent = $unit->getParent();
        if (!empty($parent)) {
            self::loadUnit($parent, $loaded);
        }
    }

    /**
     * @return Unit[]
     */
    public function loadAll(): array {
        /** @var Unit[] $units */
        $units = $this->createQueryBuilder('u', 'u.code')
            ->addSelect('c')
            ->addSelect('p')
            ->leftJoin('u.children', 'c', Join::WITH, 'c.deleted = FALSE')
            ->leftJoin('u.parent', 'p', Join::WITH, 'p.deleted = FALSE')
            ->where('u.deleted = FALSE')
            ->getQuery()
            ->getResult();
        $loaded = [];
        foreach ($units as $unit) {
            self::loadUnit($unit, $loaded);
        }
        return $units;
    }
}
