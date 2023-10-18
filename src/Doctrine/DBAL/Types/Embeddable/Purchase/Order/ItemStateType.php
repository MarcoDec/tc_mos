<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Purchase\Order;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class ItemStateType extends StateType {
    final public const TYPES = [
        self::TYPE_STATE_AGREED,
        self::TYPE_STATE_DELIVERED,
        self::TYPE_STATE_DRAFT,
        self::TYPE_STATE_FORECAST,
        self::TYPE_STATE_INITIAL,
        self::TYPE_STATE_MONTHLY,
        self::TYPE_STATE_PARTIALLY_DELIVERED,
        self::TYPE_STATE_LOCKED
    ];

    public function getName(): string {
        return 'purchase_order_item_state';
    }
}
