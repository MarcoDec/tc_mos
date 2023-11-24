<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Selling\Order;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class SellingOrderItemStateType extends StateType {
    final public const TYPES = [
        self::TYPE_STATE_AGREED,
        self::TYPE_STATE_BILLED,
        self::TYPE_STATE_DELIVERED,
        self::TYPE_STATE_DRAFT,
        self::TYPE_STATE_PAID,
        self::TYPE_STATE_PARTIALLY_DELIVERED
    ];

    public function getName(): string {
        return 'selling_order_item_state';
    }
}
