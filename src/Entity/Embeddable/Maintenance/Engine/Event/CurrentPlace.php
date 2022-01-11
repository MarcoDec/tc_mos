<?php

namespace App\Entity\Embeddable\Maintenance\Engine\Event;

use App\Entity\Embeddable\AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    public const WF_PLACE_CLOSED = 'closed';
    public const WF_PLACE_ONGOING = 'accepted';
    public const WF_TR_CLOSE = 'close';

    final public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : self::WF_PLACE_ONGOING);
    }

    final public function getTrafficLight(): int {
        switch ($this->getName()) {
            case self::WF_PLACE_CLOSED:
                return 1;
            default:
                return 2;
        }
    }
}
