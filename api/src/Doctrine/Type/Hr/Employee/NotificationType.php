<?php

declare(strict_types=1);

namespace App\Doctrine\Type\Hr\Employee;

use App\Doctrine\Type\EnumType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class NotificationType extends EnumType {
    /** @param EnumNotificationType $value */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        return $value->value;
    }

    /** @param null|string $value */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?EnumNotificationType {
        return $value === null ? $value : EnumNotificationType::from($value);
    }

    public function getName(): string {
        return 'notification';
    }

    /** @return string[] */
    protected function getEnumValues(): array {
        return EnumNotificationType::values();
    }
}
