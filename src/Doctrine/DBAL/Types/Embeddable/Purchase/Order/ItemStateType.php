<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Purchase\Order;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class ItemStateType extends StateType {
    final public const TYPES = [
        self::TYPE_STATE_AGREED,
        self::TYPE_STATE_RECEIVED,
        self::TYPE_STATE_DRAFT,
        self::TYPE_STATE_FORECAST,
        self::TYPE_STATE_INITIAL,
        self::TYPE_STATE_MONTHLY,
        self::TYPE_STATE_PARTIALLY_RECEIVED,
        self::TYPE_STATE_PAID
    ];

    public function getName(): string {
        return 'purchase_order_item_state';
    }
}
