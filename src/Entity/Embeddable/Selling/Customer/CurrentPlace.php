<?php

namespace App\Entity\Embeddable\Selling\Customer;

use App\Entity\Embeddable\AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    public const WF_PLACE_BLOCKED = 'blocked';
    public const WF_PLACE_DISABLED = 'disabled';
    public const WF_PLACE_ENABLED = 'enabled';
    public const WF_PLACE_IN_CREATION = 'in_creation';
    public const WF_TR_BLOCK = 'block';
    public const WF_TR_DISABLE = 'disable';
    public const WF_TR_ENABLE = 'enable';

    final public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : self::WF_PLACE_IN_CREATION);
    }

    final public function getTrafficLight(): int {
        switch ($this->getName()) {
            case self::WF_PLACE_BLOCKED:
            case self::WF_PLACE_DISABLED:
                return 3;
            case self::WF_PLACE_ENABLED:
                return 1;
            default:
                return 2;
        }
    }
}
