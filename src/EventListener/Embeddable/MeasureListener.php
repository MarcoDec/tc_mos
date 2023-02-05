<?php

namespace App\EventListener\Embeddable;

use App\Entity\Interfaces\MeasuredInterface;
use App\Entity\Purchase\Order\Item;
use App\Entity\Purchase\Order\Order;
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
        if ($entity instanceof Order) {
           /** @var Item $item */
           foreach ($entity->getItems() as $item){
              $this->hydrator->hydrateIn($item);
           }
        }
    }
}