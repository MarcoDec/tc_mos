<?php

namespace App\EventListener\Management;

use App\Entity\Management\AbstractUnit;
use App\Entity\Management\Currency;
use App\Entity\Management\Unit;
use App\Repository\CurrencyRepository;
use App\Repository\Management\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;

final class UnitListener {
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    public function postLoad(AbstractUnit $tree): void {
        /** @var CurrencyRepository|UnitRepository $repo */
        $repo = $this->em->getRepository($tree instanceof Unit ? Unit::class : Currency::class);
        $tree->setRepo($repo);
    }
}
