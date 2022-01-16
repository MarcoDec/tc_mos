<?php

namespace App\Entity\Embeddable\Purchase\Order\Item;

use App\Entity\Embeddable\AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    public const WF_PLACE_CANCELLED = 'cancelled';
    public const WF_PLACE_CONFIRMED = 'confirmed';
    public const WF_PLACE_CREATED = 'created';
    public const WF_PLACE_DELAY = 'delay';
    public const WF_PLACE_DONE = 'done';
    public const WF_PLACE_FORECAST = 'forecast';
    public const WF_PLACE_MANUAL = 'manual';
    public const WF_PLACE_MONTHLY = 'monthly';
    public const WF_PLACE_PARTIAL_DELIVERY = 'partial_delivery';
    public const WF_PLACE_SAVED = 'saved';
    public const WF_TR_CANCEL = 'cancel';
    public const WF_TR_CLOSE = 'close';
    public const WF_TR_CONFIRM = 'confirm';
    public const WF_TR_DELAYED = 'delayed';
    public const WF_TR_DELIVER = 'deliver';
    public const WF_TR_FORECAST = 'forecast';
    public const WF_TR_MANUAL = 'manual';
    public const WF_TR_MONTHLY = 'monthly';
    public const WF_TR_SAVE = 'save';

    final public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : self::WF_PLACE_CREATED);
    }

    final public function getTrafficLight(): int {
        switch ($this->getName()) {
            case self::WF_PLACE_CANCELLED:
                return 3;
            case self::WF_PLACE_CREATED:
            case self::WF_PLACE_PARTIAL_DELIVERY:
                return 1;
            default:
                return 2;
        }
    }
}
