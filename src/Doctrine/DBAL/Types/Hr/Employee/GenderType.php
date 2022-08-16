<?php

namespace App\Doctrine\DBAL\Types\Hr\Employee;

use App\Doctrine\DBAL\Types\EnumType;

final class GenderType extends EnumType {
    public const TYPE_FEMALE = 'female';
    public const TYPE_MALE = 'male';
    public const TYPES = [self::TYPE_FEMALE, self::TYPE_MALE];

    public function getName(): string {
        return 'gender_place';
    }
}
