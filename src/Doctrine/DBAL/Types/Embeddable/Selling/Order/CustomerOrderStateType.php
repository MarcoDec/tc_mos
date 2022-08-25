<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Selling\Order;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class CustomerOrderStateType extends StateType {
    final public const TYPES = [
        self::TYPE_STATE_AGREED,
        self::TYPE_STATE_BLOCKED,
        self::TYPE_STATE_CLOSED,
        self::TYPE_STATE_DELIVERED,
        self::TYPE_STATE_DRAFT,
        self::TYPE_STATE_ENABLED,
        self::TYPE_STATE_PARTIALLY_DELIVERED,
        self::TYPE_STATE_TO_VALIDATE
    ];

    public function getName(): string {
        return 'customer_order_state';
    }
}
