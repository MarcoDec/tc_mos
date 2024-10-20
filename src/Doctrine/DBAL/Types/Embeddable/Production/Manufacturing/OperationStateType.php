<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Production\Manufacturing;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class OperationStateType extends StateType {
    final public const TYPES = [
        self::TYPE_STATE_AGREED,
        self::TYPE_STATE_DRAFT,
        self::TYPE_STATE_WARNING,
        self::TYPE_STATE_LOCKED];

    public function getName(): string {
        return 'manufacturing_operation_state';
    }
}
