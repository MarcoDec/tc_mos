<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Manufacturing;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class OrderStateType extends StateType {
    final public const TYPES = [self::TYPE_STATE_AGREED, self::TYPE_STATE_ASKED, self::TYPE_STATE_REJECTED];

    public function getName(): string {
        return 'manufacturing_order_state';
    }
}
