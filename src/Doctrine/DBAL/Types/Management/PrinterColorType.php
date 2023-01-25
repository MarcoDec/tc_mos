<?php

namespace App\Doctrine\DBAL\Types\Management;

use App\Doctrine\DBAL\Types\EnumType;

final class PrinterColorType extends EnumType {
    final public const TYPE_GREEN = 'green';
    final public const TYPE_YELLOW = 'yellow';
    final public const TYPES = [self::TYPE_GREEN, self::TYPE_YELLOW];

    public function getName(): string {
        return 'printer_color';
    }
}
