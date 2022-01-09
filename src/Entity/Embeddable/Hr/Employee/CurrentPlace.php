<?php

namespace App\Entity\Embeddable\Hr\Employee;

use App\Entity\Embeddable\AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    public const WF_PLACE_BLOCKED = 'blocked';
    public const WF_PLACE_DISABLED = 'disabled';
    public const WF_PLACE_ENABLED = 'enabled';
    public const WF_PLACE_WARNING = 'warning';
    public const WF_TR_BLOCK = 'block';
    public const WF_TR_DISABLE = 'disable';
    public const WF_TR_ENABLE = 'enable';
    public const WF_TR_WARN = 'warn';

    final public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : self::WF_PLACE_WARNING);
    }

    final public function getTrafficLight(): int {
        switch ($this->getName()) {
            case self::WF_PLACE_DISABLED:
            case self::WF_PLACE_BLOCKED:
                return 3;
            case self::WF_PLACE_ENABLED:
                return 1;
            default:
                return 2;
        }
    }
}
