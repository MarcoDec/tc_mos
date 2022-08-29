<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Manufacturing;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class OperationStateType extends StateType {
    final public const TYPES = [self::TYPE_STATE_AGREED, self::TYPE_STATE_DRAFT, self::TYPE_STATE_WARNING];

    public function getName(): string {
        return 'manufacturing_operation_state';
    }
}
