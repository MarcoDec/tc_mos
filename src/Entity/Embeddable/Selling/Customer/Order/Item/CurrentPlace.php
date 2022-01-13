<?php

namespace App\Entity\Embeddable\Selling\Customer\Order\Item;

use App\Entity\Embeddable\AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    public const WF_PLACE_BILLED = 'billed';
    public const WF_PLACE_BLOCKED = 'blocked';
    public const WF_PLACE_CANCELLED = 'cancelled';
    public const WF_PLACE_CONFIRMED = 'confirmed';
    public const WF_PLACE_DELIVERED = 'delivered';
    public const WF_PLACE_PARTIAL_DELIVERED = 'partial_delivered';
    public const WF_PLACE_SAVED = 'saved';
    public const WF_TR_BILL = 'bill';
    public const WF_TR_BLOCK = 'block';
    public const WF_TR_CANCEL = 'cancel';
    public const WF_TR_CONFIRM = 'confirm';
    public const WF_TR_DELIVER = 'deliver';
    public const WF_TR_PARTIAL_DELIVER = 'partial_deliver';

    final public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : self::WF_PLACE_SAVED);
    }

    final public function getTrafficLight(): int {
        switch ($this->getName()) {
            case self::WF_PLACE_BLOCKED:
            case self::WF_PLACE_CANCELLED:
            case self::WF_PLACE_BILLED:
                return 3;
            case self::WF_PLACE_CONFIRMED:
            case self::WF_PLACE_PARTIAL_DELIVERED:
            case self::WF_PLACE_DELIVERED:
                return 1;
            default:
                return 2;
        }
    }
}
