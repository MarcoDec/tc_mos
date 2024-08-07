<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Selling\Customer;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class CustomerStateType extends StateType {
    final public const TYPES = [self::TYPE_STATE_AGREED, self::TYPE_STATE_DRAFT, self::TYPE_STATE_CLOSED];

    public function getName(): string {
        
        return 'customer_state';
    }
}
