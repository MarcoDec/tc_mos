<?php

namespace App\Doctrine\DBAL\Types;

final class EmployeeEngineCurrentPlaceType extends CurrentPlaceType {
    public const TYPES = [
        self::TYPE_BLOCKED,
        self::TYPE_DISABLED,
        self::TYPE_ENABLED,
        self::TYPE_WARNING
    ];

    public function getName(): string {
        return 'employee_engine_current_place';
    }
}
