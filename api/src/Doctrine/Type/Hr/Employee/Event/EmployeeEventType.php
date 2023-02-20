<?php

declare(strict_types=1);

namespace App\Doctrine\Type\Hr\Employee\Event;

use App\Doctrine\Type\EnumType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class EmployeeEventType extends EnumType {
    /** @param EnumEmployeeEventType $value */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value->value;
    }

    /** @param null|string $value */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?EnumEmployeeEventType {
        return $value === null ? $value : EnumEmployeeEventType::from($value);
    }

    public function getName(): string {
        return 'employee_event';
    }

    /** @return string[] */
    protected function getEnumValues(): array {
        return EnumEmployeeEventType::values();
    }
}
