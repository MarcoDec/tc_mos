<?php

namespace App\Doctrine\EventListener\Embeddable;

use App\Doctrine\Entity\Interfaces\MeasuredInterface;
use App\Doctrine\Service\MeasureHydrator;

final class MeasureListener {
    public function __construct(private MeasureHydrator $hydrator) {
    }

    public function postLoad(MeasuredInterface $entity): void {
        $this->hydrator->hydrateIn($entity);
    }
}
