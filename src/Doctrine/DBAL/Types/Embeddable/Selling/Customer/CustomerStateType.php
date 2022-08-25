<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Selling\Customer;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class CustomerStateType extends StateType {
    final public const TYPES = [
        self::TYPE_STATE_AGREED,
        self::TYPE_STATE_BLOCKED,
        self::TYPE_STATE_DISABLED,
        self::TYPE_STATE_DRAFT,
        self::TYPE_STATE_ENABLED
    ];

    public function getName(): string {
        return 'customer_state';
    }
}
