<?php

namespace App\Doctrine\DBAL\Types\Selling\Customer;

use App\Doctrine\DBAL\Types\CurrentPlaceType as AbstractCurrentPlaceType;

final class CurrentPlaceType extends AbstractCurrentPlaceType {
    public const TYPES = [
        self::TYPE_AGREED,
        self::TYPE_BLOCKED,
        self::TYPE_DISABLED,
        self::TYPE_DRAFT
    ];

    public function getName(): string {
        return 'customer_current_place';
    }
}
