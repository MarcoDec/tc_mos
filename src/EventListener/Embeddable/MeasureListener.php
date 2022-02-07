<?php

namespace App\EventListener\Embeddable;

use App\Entity\Interfaces\MeasuredInterface;
use App\Service\MeasureHydrator;

final class MeasureListener {
    public function __construct(private MeasureHydrator $hydrator) {
    }

    public function postLoad(MeasuredInterface $entity): void {
        $this->hydrator->hydrateIn($entity);
    }
}
