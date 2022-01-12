<?php

namespace App\Entity\Embeddable\Purchase\Order;

use App\Entity\Embeddable\AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    public const WF_PLACE_BLOCKED = 'blocked';
    public const WF_PLACE_CANCELLED = 'cancelled';
    public const WF_PLACE_CART = 'cart';
    public const WF_PLACE_CONFIRMED = 'confirmed';
    public const WF_PLACE_DONE = 'done';
    public const WF_PLACE_INITIAL = 'initial';
    public const WF_PLACE_PARTIAL_DELIVERY = 'partial_delivery';
    public const WF_PLACE_SAVED = 'saved';
    public const WF_TR_CANCEL = 'cancel';
    public const WF_TR_CART = 'cart';
    public const WF_TR_CLOSE = 'close';
    public const WF_TR_CONFIRM = 'confirm';
    public const WF_TR_DELIVER = 'deliver';
    public const WF_TR_LOCK = 'lock';
    public const WF_TR_SAVE = 'save';
    public const WF_TR_UNLOCK = 'unlock';

    final public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : self::WF_PLACE_INITIAL);
    }

    final public function getTrafficLight(): int {
        switch ($this->getName()) {
            case self::WF_PLACE_BLOCKED:
            case self::WF_PLACE_CANCELLED:
                return 3;
            case self::WF_PLACE_CART:
            case self::WF_PLACE_INITIAL:
            case self::WF_PLACE_PARTIAL_DELIVERY:
                return 1;
            default:
                return 2;
        }
    }
}
