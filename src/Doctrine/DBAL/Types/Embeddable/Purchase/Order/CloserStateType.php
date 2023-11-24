<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Purchase\Order;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class CloserStateType extends StateType {
    final public const TYPES = [
        self::TYPE_STATE_BLOCKED,
        self::TYPE_STATE_CLOSED,
        self::TYPE_STATE_DELAYED,
        self::TYPE_STATE_ENABLED
    ];

    public function getName(): string {
        return 'purchase_order_item_closer_state';
    }
}
