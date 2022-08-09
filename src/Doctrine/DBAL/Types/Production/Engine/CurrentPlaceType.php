<?php

namespace App\Doctrine\DBAL\Types\Production\Engine;

use App\Doctrine\DBAL\Types\CurrentPlaceType as AbstractCurrentPlaceType;

final class CurrentPlaceType extends AbstractCurrentPlaceType {
    public const TYPES = [self::TYPE_AGREED, self::TYPE_CLOSED, self::TYPE_DRAFT, self::TYPE_REJECTED];

    public function getName(): string {
        return 'engine_event_current_place';
    }
}