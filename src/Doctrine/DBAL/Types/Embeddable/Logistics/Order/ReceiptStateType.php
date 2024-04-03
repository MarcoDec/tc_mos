<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Logistics\Order;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class ReceiptStateType extends StateType {
    final public const TYPES = [
        self::TYPE_STATE_ASKED,
        self::TYPE_STATE_REJECTED,
        self::TYPE_STATE_CLOSED,
        self::TYPE_STATE_TO_VALIDATE
    ];

    public function getName(): string {
        return 'receipt_state';
    }
}
