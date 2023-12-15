<?php

namespace App\Doctrine\DBAL\Types\Hr\Employee;

use App\Doctrine\DBAL\Types\EnumType;

final class NotificationCategoryType extends EnumType {
    final public const TYPE_DEFAULT = 'default';
    final public const TYPES = [self::TYPE_DEFAULT];

    public function getName(): string {
        return 'notification_category';
    }
}
