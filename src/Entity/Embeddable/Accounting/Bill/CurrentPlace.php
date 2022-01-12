<?php

namespace App\Entity\Embeddable\Accounting\Bill;

use App\Entity\Embeddable\AbstractCurrentPlace;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class CurrentPlace extends AbstractCurrentPlace {
    public const WF_PLACE_BILLED = 'billed';
    public const WF_PLACE_CANCELLED = 'cancelled';
    public const WF_PLACE_DRAFT = 'draft';
    public const WF_PLACE_LITIGATION = 'litigation';
    public const WF_PLACE_PAID = 'paid';
    public const WF_PLACE_PARTIALLY_PAID = 'partially_paid';
    public const WF_TR_CANCEL = 'cancel';
    public const WF_TR_TO_BILL = 'to_bill';
    public const WF_TR_TO_BILL_FROM_LITIGATION = 'to_bill_from_litigation';
    public const WF_TR_TO_LITIGATION = 'to_litigation';
    public const WF_TR_TO_PAID = 'to_paid';
    public const WF_TR_TO_PARTIALLY_PAID = 'to_partially_paid';
    public const WF_TR_UNBILL = 'unbill';

    final public function __construct(?string $name = null) {
        parent::__construct(!empty($name) ? $name : self::WF_PLACE_DRAFT);
    }

    final public function getTrafficLight(): int {
        switch ($this->getName()) {
            case self::WF_PLACE_LITIGATION:
            case self::WF_PLACE_CANCELLED:
                return 3;
            case self::WF_PLACE_PARTIALLY_PAID:
            case self::WF_PLACE_PAID:
                return 1;
            default:
                return 2;
        }
    }
}
