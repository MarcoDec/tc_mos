<?php

namespace App\Entity\Embeddable\Production\Manufacturing;

use App\Entity\Embeddable\AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    public const WF_PLACE_BLOCKED = 'blocked';
    public const WF_PLACE_CANCELLED = 'cancelled';
    public const WF_PLACE_CONFIRMED = 'confirmed';
    public const WF_PLACE_DONE = 'done';
    public const WF_PLACE_DRAFT = 'draft';
    public const WF_TR_BLOCK = 'block';
    public const WF_TR_CANCEL = 'cancel';
    public const WF_TR_CLOSE = 'close';
    public const WF_TR_CONFIRM = 'confirm';
    public const WF_TR_UNBLOCK = 'unblock';

    final public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : self::WF_PLACE_DRAFT);
    }

    final public function getTrafficLight(): int {
        switch ($this->getName()) {
            case self::WF_PLACE_CANCELLED:
            case self::WF_PLACE_DONE:
                return 3;
            case self::WF_PLACE_CONFIRMED:
                return 1;
            default:
                return 2;
        }
    }
}
