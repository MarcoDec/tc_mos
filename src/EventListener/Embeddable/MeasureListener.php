<?php

namespace App\EventListener\Embeddable;

use App\Entity\Interfaces\MeasuredInterface;
use App\Service\MeasureHydrator;
use Doctrine\ORM\Event\LifecycleEventArgs;

final class MeasureListener {
    public function __construct(private readonly MeasureHydrator $hydrator) {
    }

    public function postLoad(LifecycleEventArgs $event): void {
        $entity = $event->getObject();
        if ($entity instanceof MeasuredInterface) {
            $this->hydrator->hydrateIn($entity);
        }
    }
}
