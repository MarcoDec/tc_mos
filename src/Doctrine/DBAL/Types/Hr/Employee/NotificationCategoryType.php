<?php

namespace App\Doctrine\DBAL\Types\Hr\Employee;

use App\Doctrine\DBAL\Types\EnumType;

final class NotificationCategoryType extends EnumType {
    public const TYPE_DEFAULT = 'default';
    public const TYPES = [self::TYPE_DEFAULT];

    public function getName(): string {
        return 'notification_category';
    }
}
