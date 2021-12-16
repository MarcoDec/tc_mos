<?php

namespace App\Doctrine\DBAL\Types\Hr\Employee;

use App\Doctrine\DBAL\Types\Type;

final class CurrentPlaceType extends Type {
    public const TYPE_BLOCKED = 'blocked';
    public const TYPE_DISABLED = 'disabled';
    public const TYPE_ENABLED = 'enabled';
    public const TYPE_WARNING = 'warning';
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
