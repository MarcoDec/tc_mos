<?php

namespace App\Repository\Management;

use App\Entity\Management\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class UnitRepository extends NestedTreeRepository {
    public function __construct(EntityManagerInterface $em) {
        parent::__construct($em, $em->getClassMetadata(Unit::class));
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
        foreach ($units as $unit) {
            $unit->setRepo(null);
        }
        return $units;
    }
}
