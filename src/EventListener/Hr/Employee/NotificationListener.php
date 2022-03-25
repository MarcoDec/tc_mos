<?php

namespace App\EventListener\Hr\Employee;

use App\Entity\Hr\Employee\Notification;
use Doctrine\ORM\Event\LifecycleEventArgs;

final class NotificationListener {
    public function preRemove(Notification $notification, LifecycleEventArgs $event): void {
        $notification->setRead(true);
        $event->getEntityManager()->flush();
    }
}
