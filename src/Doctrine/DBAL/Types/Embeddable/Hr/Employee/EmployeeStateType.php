<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Hr\Employee;

use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class EmployeeStateType extends StateType {
    final public const TYPES = [self::TYPE_STATE_AGREED, self::TYPE_STATE_WARNING, self::TYPE_STATE_CLOSED];

    public function getName(): string {
        return 'employee_state';

    }
}
