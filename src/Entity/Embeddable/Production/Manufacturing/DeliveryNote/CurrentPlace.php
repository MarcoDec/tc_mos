<?php

namespace App\Entity\Embeddable\Production\Manufacturing\DeliveryNote;

use App\Entity\Embeddable\AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    public const WF_PLACE_IN_CREATION = 'in_creation';
    public const WF_PLACE_READY_TO_SEND = 'ready_to_send';
    public const WF_PLACE_SENT = 'sent';
    public const WF_TR_SEND = 'send';
    public const WF_TR_VALIDATE = 'validate';

    final public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : self::WF_PLACE_IN_CREATION);
    }

    final public function getTrafficLight(): int {
        switch ($this->getName()) {
            case self::WF_PLACE_SENT:
                return 2;
            default:
                return 1;
        }
    }
}
