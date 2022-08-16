<?php

namespace App\Doctrine\DBAL\Types\Production\Manufacturing\Operation;

use App\Doctrine\DBAL\Types\CurrentPlaceType as AbstractCurrentPlaceType;

final class CurrentPlaceType extends AbstractCurrentPlaceType {
    public const TYPES = [
        self::TYPE_AGREED,
        self::TYPE_BLOCKED,
        self::TYPE_CLOSED,
        self::TYPE_DRAFT,
        self::TYPE_UNDER_EXEMPTION
    ];

    public function getName(): string {
        return 'manufacturing_operation_current_place';
    }
}
