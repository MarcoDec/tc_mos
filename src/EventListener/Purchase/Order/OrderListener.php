<?php

namespace App\EventListener\Purchase\Order;

use App\Entity\Purchase\Order\Order;
use Symfony\Component\Workflow\Event\GuardEvent;

final class OrderListener {
    public function guardDeliver(GuardEvent $event): void {
        /** @var Order $order */
        $order = $event->getSubject();
        if ($order->isNotReceipt()) {
            $event->setBlocked(true, 'La commande n\'a aucune réception.');
        }
    }

    public function guardPartiallyDeliver(GuardEvent $event): void {
        /** @var Order $order */
        $order = $event->getSubject();
        if ($order->hasNoReceipt()) {
            $event->setBlocked(true, 'La commande n\'a aucune réception.');
        }
    }
}
