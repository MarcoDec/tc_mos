<?php

namespace App\Doctrine\DBAL\Types\Embeddable\Hr\Event;

use App\Doctrine\DBAL\Types\Embeddable\BlockerStateType;
use App\Doctrine\DBAL\Types\Embeddable\EmployeeEngineStateType;
use App\Doctrine\DBAL\Types\Embeddable\StateType;

final class EventStateType extends StateType {
    final public const TYPES = [...BlockerStateType::TYPES, ...EmployeeEngineStateType::TYPES];

    public function getName(): string {
        return 'employee_event_state';
    }
}
