<?php

namespace App\Doctrine\DBAL\Types\Embeddable;

final class EmployeeEngineStateType extends StateType {
    final public const TYPES = [self::TYPE_STATE_AGREED, self::TYPE_STATE_WARNING];

    public function getName(): string {
        return 'employee_engine_state';
    }
}
