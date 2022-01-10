<?php

namespace App\Entity\Embeddable\Production\Engine;

use App\Entity\Embeddable\AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    public const WF_PLACE_DISABLED = 'disabled';
    public const WF_PLACE_ENABLED = 'enabled';
    public const WF_PLACE_END_OF_LIFE = 'end_of_life';
    public const WF_PLACE_LOCKED = 'locked';
    public const WF_PLACE_WARNING = 'warning';
    public const WF_TR_DISABLE = 'disable';
    public const WF_TR_ENABLE = 'enable';
    public const WF_TR_LOCK = 'lock';
    public const WF_TR_SUPERVISE = 'supervise';
    public const WF_TR_THROW_AWAY = 'throw_away';
    public const WF_TR_UNLOCK = 'unlock';

    final public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : self::WF_PLACE_WARNING);
    }

    final public function getTrafficLight(): int {
        switch ($this->getName()) {
            case self::WF_PLACE_ENABLED:
                return 1;
            case self::WF_PLACE_WARNING:
                return 2;
            default:
                return 3;
        }
    }
}
