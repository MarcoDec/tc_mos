<?php

namespace App\Entity\Embeddable\Purchase\Supplier;

use App\Entity\Embeddable\AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    public const WF_PLACE_BLOCKED = 'blocked';
    public const WF_PLACE_DRAFTED = 'drafted';
    public const WF_PLACE_INACTIVE = 'inactive';
    public const WF_PLACE_POTENTIAL = 'potential';
    public const WF_PLACE_SUPERVISED = 'supervised';
    public const WF_PLACE_VALIDATED = 'validated';
    public const WF_TR_BLOCK = 'block';
    public const WF_TR_DEACTIVATE = 'deactivate';
    public const WF_TR_POTENTIAL = 'potential';
    public const WF_TR_SUPERVISE = 'supervise';
    public const WF_TR_UNLOCK = 'unlock';
    public const WF_TR_VALIDATE = 'validate';

    final public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : self::WF_PLACE_DRAFTED);
    }

    final public function getTrafficLight(): int {
        switch ($this->getName()) {
            case self::WF_PLACE_BLOCKED:
            case self::WF_PLACE_INACTIVE:
                return 3;
            case self::WF_PLACE_VALIDATED:
                return 1;
            default:
                return 2;
        }
    }
}
