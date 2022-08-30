<?php

namespace App\Doctrine\DBAL\Types\Production\Engine;

use App\Doctrine\DBAL\Types\EnumType;

final class EventType extends EnumType {
    final public const TYPE_MAINTENANCE = 'maintenance';
    final public const TYPE_REQUEST = 'request';
    final public const TYPES = [self::TYPE_MAINTENANCE, self::TYPE_REQUEST];

    public function getName(): string {
        return 'engine_event';
    }
}
