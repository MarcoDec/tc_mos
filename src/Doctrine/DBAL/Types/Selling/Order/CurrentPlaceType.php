<?php

namespace App\Doctrine\DBAL\Types\Selling\Order;

use App\Doctrine\DBAL\Types\CurrentPlaceType as AbstractCurrentPlaceType;

final class CurrentPlaceType extends AbstractCurrentPlaceType {
    public const TYPES = [
        self::TYPE_AGREED,
        self::TYPE_BLOCKED,
        self::TYPE_CLOSED,
        self::TYPE_DISABLED,
        self::TYPE_DRAFT,
        self::TYPE_PARTIALLY_DELIVERED,
        self::TYPE_TO_VALIDATE
    ];

    public function getName(): string {
        return 'customer_order_current_place';
    }
}