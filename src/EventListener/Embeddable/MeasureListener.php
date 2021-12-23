<?php

namespace App\EventListener\Embeddable;

use App\Entity\Embeddable\Measure;
use App\Entity\Management\Unit;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;

final class MeasureListener {

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function postLoad(LifecycleEventArgs $args): void {
        $entity = $args->getObject();

        // Check if there is any Measure in the entity
        $measureExists = false;
        if(method_exists($entity, 'getEmbeddedMeasures') && count($entity->getEmbeddedMeasures()) >= 1) {
            $measureExists = true;
        }

        // If we got measures inside the entity
        // Then set the measure Unit and the denominator if any
        if($measureExists) {
            foreach($entity->getEmbeddedMeasures() as $measure) {
                
                $unit = $this->em->getRepository(Unit::class)->findOneByCode($measure->getCode());

                // If this is a composed unit
                if(null !== $unit->getDenominator()) {
                    $measure->setDenominator($unit->getDenominator());
                }

                if(null !== $unit) {
                    $measure->setUnit($unit);
                }

            }
        }
    }
}