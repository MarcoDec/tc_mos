<?php

namespace App\Doctrine\DBAL\Types\Purchase\Order\Item;

use App\Doctrine\DBAL\Types\CurrentPlaceType as AbstractCurrentPlaceType;

final class CurrentPlaceType extends AbstractCurrentPlaceType {
    public const TYPES = [
        self::TYPE_AGREED,
        self::TYPE_CLOSED,
        self::TYPE_DELAY,
        self::TYPE_DISABLED,
        self::TYPE_DRAFT,
        self::TYPE_FORECAST,
        self::TYPE_MANUAL,
        self::TYPE_MONTHLY,
        self::TYPE_PARTIALLY_DELIVERED,
        self::TYPE_TO_VALIDATE
    ];

    public function getName(): string {
        return 'supplier_order_item_current_place';
    }
}
