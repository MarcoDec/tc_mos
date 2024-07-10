<?php

namespace App\Doctrine\DBAL\Types\Hr\Employee;

use App\Doctrine\DBAL\Types\CurrentPlaceType as AbstractCurrentPlaceType;

final class CurrentPlaceType extends AbstractCurrentPlaceType {
    public const TYPES = [
        self::TYPE_BLOCKED,
        self::TYPE_DISABLED,
        self::TYPE_ENABLED,
        self::TYPE_WARNING
    ];

    public function getName(): string {
        return 'employee_current_place';
    }
}
