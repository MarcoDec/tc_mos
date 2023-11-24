<?php

namespace App\Doctrine\DBAL\Types\Hr\Employee;

use App\Doctrine\DBAL\Types\EnumType;

final class SituationType extends EnumType {
    final public const TYPE_MARRIED = 'married';
    final public const TYPE_SINGLE = 'single';
    final public const TYPE_WINDOWED = 'windowed';
    final public const TYPES = [self::TYPE_MARRIED, self::TYPE_SINGLE, self::TYPE_WINDOWED];

    public function getName(): string {
        return 'situation_place';
    }
}
