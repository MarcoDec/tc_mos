<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Purchase\Order;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class OrderStateType extends StateType {
    final public const TYPES = [
        self::TYPE_STATE_AGREED,
        self::TYPE_STATE_CART,
        self::TYPE_STATE_RECEIVED,
        self::TYPE_STATE_DRAFT,
        self::TYPE_STATE_INITIAL,
        self::TYPE_STATE_PARTIALLY_RECEIVED,
        self::TYPE_STATE_PAID
    ];

    public function getName(): string {
        return 'purchase_order_state';
    }
}
