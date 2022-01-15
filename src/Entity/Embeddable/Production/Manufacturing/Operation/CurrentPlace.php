<?php

namespace App\Entity\Embeddable\Production\Manufacturing\Operation;

use App\Entity\Embeddable\AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    public const WF_PLACE_BLOCKED = 'blocked';
    public const WF_PLACE_CLOSED = 'closed';
    public const WF_PLACE_EXEMPTIONED = 'exemptioned';
    public const WF_PLACE_OK = 'ok';
    public const WF_PLACE_STARTED = 'started';
    public const WF_TR_BLOCK = 'block';
    public const WF_TR_CLOSE = 'close';
    public const WF_TR_CONFIRM = 'confirm';
    public const WF_TR_EXEMPTION = 'exemption';

    final public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : self::WF_PLACE_STARTED);
    }

    final public function getTrafficLight(): int {
        switch ($this->getName()) {
            case self::WF_PLACE_BLOCKED:
            case self::WF_PLACE_CLOSED:
                return 3;
            case self::WF_PLACE_OK:
                return 1;
            default:
                return 2;
        }
    }
}
