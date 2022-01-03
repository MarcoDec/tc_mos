<?php

namespace App\EventListener\Embeddable;

use App\Entity\Interfaces\EmbeddedInterface;
use App\Entity\Management\Unit;
use Doctrine\ORM\EntityManagerInterface;

final class MeasureListener {
    public function __construct(private EntityManagerInterface $em) {
    }

    public function postLoad(EmbeddedInterface $entity): void {
        $unitRepo = $this->em->getRepository(Unit::class);
        foreach ($entity->getEmbeddedMeasures() as $measure) {
            if ($measure->getCode() !== null) {
                $measure->setUnit($unitRepo->findOneBy(['code' => $measure->getCode()]));
            }
            if ($measure->getDenominator() !== null) {
                $measure->setDenominatorUnit($unitRepo->findOneBy(['code' => $measure->getDenominator()]));
            }
        }
    }
}
