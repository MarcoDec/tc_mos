<?php

namespace App\EventListener\Purchase\Order;

use App\Entity\Embeddable\Purchase\Order\Order\State;
use App\Entity\Purchase\Order\ComponentItem;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Event\GuardEvent;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Registry;

final class ItemListener {
    public function __construct(private readonly Registry $registry) {
    }

    public function guardDeliver(GuardEvent $event): void {
        /** @var ComponentItem $item */
        $item = $event->getSubject();
        if ($item->isNotReceipt()) {
            $event->setBlocked(true, 'La ligne n\'est pas totalement réceptionnée.');
        }
    }

    public function guardPartiallyDeliver(GuardEvent $event): void {
        /** @var ComponentItem $item */
        $item = $event->getSubject();
        if ($item->hasNoReceipt()) {
            $event->setBlocked(true, 'La ligne n\'a aucune réception.');
        }
    }

    public function onDeliver(CompletedEvent $event): void {
        /** @var ComponentItem $item */
        $item = $event->getSubject();
        $order = $item->getOrder();
        if (empty($order)) {
            throw new LogicException('Item with no order given.');
        }
        $workflow = $this->registry->get($order, 'purchase_order');
        if ($workflow->can($order, State::TR_DELIVER)) {
            $workflow->apply($order, State::TR_DELIVER);
        } elseif ($workflow->can($order, State::TR_PARTIALLY_DELIVER)) {
            $workflow->apply($order, State::TR_PARTIALLY_DELIVER);
        }
    }
}
