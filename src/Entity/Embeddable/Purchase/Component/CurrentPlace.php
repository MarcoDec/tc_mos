<?php

namespace App\Entity\Embeddable\Purchase\Component;

use App\Entity\Embeddable\AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    public const WF_PLACE_ACCEPTED = 'accepted';
    public const WF_PLACE_BLOCKED = 'blocked';
    public const WF_PLACE_ENDED = 'ended';
    public const WF_PLACE_EXEMPTIONED = 'exemptioned';
    public const WF_PLACE_INACTIVED = 'inactived';
    public const WF_PLACE_INVALIDATED = 'invalidated';
    public const WF_TR_ACCEPT = 'accept';
    public const WF_TR_BLOCK = 'block';
    public const WF_TR_END = 'end';
    public const WF_TR_EXEMPTION = 'exemption';
    public const WF_TR_INACTIVE = 'inactive';

    final public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : self::WF_PLACE_INVALIDATED);
    }

    final public function getTrafficLight(): int {
        switch ($this->getName()) {
            case self::WF_PLACE_BLOCKED:
            case self::WF_PLACE_INACTIVED:
            case self::WF_PLACE_INVALIDATED:
            case self::WF_PLACE_ENDED:
                return 3;
            case self::WF_PLACE_ACCEPTED:
                return 1;
            default:
                return 2;
        }
    }
}
