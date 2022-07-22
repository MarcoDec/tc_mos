<?php

namespace App\Doctrine\DBAL\Types\Purchase\Component;

use App\Doctrine\DBAL\Types\CurrentPlaceType as AbstractCurrentPlaceType;

final class CurrentPlaceType extends AbstractCurrentPlaceType {
    public const TYPES = [
        self::TYPE_AGREED,
        self::TYPE_BLOCKED,
        self::TYPE_DISABLED,
        self::TYPE_DRAFT,
        self::TYPE_UNDER_EXEMPTION
    ];

    public function getName(): string {
        return 'component_current_place';
    }
}
