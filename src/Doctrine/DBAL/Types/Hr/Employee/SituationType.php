<?php

namespace App\Doctrine\DBAL\Types\Hr\Employee;

use App\Doctrine\DBAL\Types\EnumType;

final class SituationType extends EnumType {
    public const TYPE_MARRIED = 'married';
    public const TYPE_SINGLE = 'single';
    public const TYPE_WINDOWED = 'windowed';
    public const TYPES = [self::TYPE_MARRIED, self::TYPE_SINGLE, self::TYPE_WINDOWED];

    public function getName(): string {
        return 'situation_place';
    }
}
