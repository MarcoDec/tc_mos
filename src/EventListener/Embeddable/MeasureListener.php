<?php

namespace App\EventListener\Embeddable;

use App\Entity\Management\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Entity\Interfaces\EmbeddedInterface;


final class MeasureListener {
    public function __construct(private EntityManagerInterface $em) {
    }

    public function postLoad(LifecycleEventArgs $args): void {
        $entity = $args->getObject();

        // Check if there is any Measure in the entity
        $measureExists = false;
        if ($entity instanceof EmbeddedInterface) {
            $measureExists = true;
        }

        // If we got measures inside the entity
        // Then set the measure Unit and the denominator if any
        if ($measureExists && count($entity->getEmbeddedMeasures())) {
            foreach ($entity->getEmbeddedMeasures() as $measure) {
                if (null !== $measure->getCode()) {
                    $unit = $this->em->getRepository(Unit::class)->findOneBy(['code' => $measure->getCode()]);

                    if (null !== $unit) {
                        // If this is a composed unit
                        if (null !== $unit->getDenominator()) {
                            $measure->setDenominator($unit->getDenominator());
                        }

                        if (null !== $unit) {
                            $measure->setUnit($unit);
                        }
                    }
                }
            }
        }
    }
}
