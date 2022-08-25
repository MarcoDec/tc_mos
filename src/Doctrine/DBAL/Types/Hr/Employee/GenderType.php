<?php

namespace App\Doctrine\DBAL\Types\Hr\Employee;

use App\Doctrine\DBAL\Types\EnumType;

final class GenderType extends EnumType {
    final public const TYPE_FEMALE = 'female';
    final public const TYPE_MALE = 'male';
    final public const TYPES = [self::TYPE_FEMALE, self::TYPE_MALE];

    public function getName(): string {
        return 'gender_place';
    }
}
