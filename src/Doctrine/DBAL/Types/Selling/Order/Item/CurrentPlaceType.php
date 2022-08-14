<?php

namespace App\Doctrine\DBAL\Types\Selling\Order\Item;

use App\Doctrine\DBAL\Types\CurrentPlaceType as AbstractCurrentPlaceType;

final class CurrentPlaceType extends AbstractCurrentPlaceType {
    public const TYPES = [
        self::TYPE_AGREED,
        self::TYPE_BLOCKED,
        self::TYPE_DISABLED,
        self::TYPE_DRAFT,
        self::TYPE_PAID,
        self::TYPE_PARTIALLY_DELIVERED,
        self::TYPE_SENT
    ];

    public function getName(): string {
        return 'customer_order_item_current_place';
    }
}
