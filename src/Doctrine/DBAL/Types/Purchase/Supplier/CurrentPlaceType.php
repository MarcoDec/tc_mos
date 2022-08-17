<?php

namespace App\Doctrine\DBAL\Types\Purchase\Supplier;

use App\Doctrine\DBAL\Types\CurrentPlaceType as AbstractCurrentPlaceType;

final class CurrentPlaceType extends AbstractCurrentPlaceType {
    public const TYPES = [
        self::TYPE_AGREED,
        self::TYPE_BLOCKED,
        self::TYPE_DISABLED,
        self::TYPE_DRAFT,
        self::TYPE_TO_VALIDATE,
        self::TYPE_WARNING
    ];

    public function getName(): string {
        return 'supplier_current_place';
    }
}
