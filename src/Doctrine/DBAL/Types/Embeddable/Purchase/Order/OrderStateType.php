<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Purchase\Order;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class OrderStateType extends StateType {
    final public const TYPES = [
        self::TYPE_STATE_AGREED,
        self::TYPE_STATE_BLOCKED,
        self::TYPE_STATE_CART,
        self::TYPE_STATE_CLOSED,
        self::TYPE_STATE_DELIVERED,
        self::TYPE_STATE_DRAFT,
        self::TYPE_STATE_ENABLED,
        self::TYPE_STATE_INITIAL,
        self::TYPE_STATE_PARTIALLY_DELIVERED
    ];

    public function getName(): string {
        return 'supplier_order_state';
    }
}
