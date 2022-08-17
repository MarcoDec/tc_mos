<?php

namespace App\Doctrine\DBAL\Types\Accounting\Bill;

use App\Doctrine\DBAL\Types\CurrentPlaceType as AbstractCurrentPlaceType;

final class CurrentPlaceType extends AbstractCurrentPlaceType {
    public const TYPES = [
        self::TYPE_BILL,
        self::TYPE_DISABLED,
        self::TYPE_DRAFT,
        self::TYPE_LITIGATION,
        self::TYPE_PAID,
        self::TYPE_PARTIALLY_PAID
    ];

    public function getName(): string {
        return 'bill_current_place';
    }
}
