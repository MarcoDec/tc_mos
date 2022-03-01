<?php

namespace App\Doctrine\Service;

use App\Doctrine\Entity\Embeddable\Measure;
use App\Doctrine\Entity\Interfaces\MeasuredInterface;
use App\Doctrine\Entity\Management\Unit;
use Doctrine\ORM\EntityManagerInterface;

final class MeasureHydrator {
    public function __construct(private EntityManagerInterface $em) {
    }

    public function hydrate(Measure $measure): Measure {
        $unitRepo = $this->em->getRepository(Unit::class);
        if ($measure->getCode() !== null) {
            $measure->setUnit($unitRepo->findOneBy(['code' => $measure->getCode()]));
        }
        if ($measure->getDenominator() !== null) {
            $measure->setDenominatorUnit($unitRepo->findOneBy(['code' => $measure->getDenominator()]));
        }
        return $measure;
    }

    public function hydrateIn(MeasuredInterface $entity): MeasuredInterface {
        foreach ($entity->getMeasures() as $measure) {
            $this->hydrate($measure);
        }
        return $entity;
    }
}
