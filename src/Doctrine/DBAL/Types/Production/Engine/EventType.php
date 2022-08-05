<?php

namespace App\Doctrine\DBAL\Types\Production\Engine;

use App\Doctrine\DBAL\Types\EnumType;

final class EventType extends EnumType {
    public const TYPE_MAINTENANCE = 'maintenance';
    public const TYPE_REQUEST = 'request';
    public const TYPES = [self::TYPE_MAINTENANCE, self::TYPE_REQUEST];

    public function getName(): string {
        return 'engine_event_type';
    }
}
