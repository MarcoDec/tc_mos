<?php

namespace App\Doctrine\DBAL\Types\Project\Product;

use App\Doctrine\DBAL\Types\CurrentPlaceType as AbstractCurrentPlaceType;

final class CurrentPlaceType extends AbstractCurrentPlaceType {
    public const TYPES = [
        self::TYPE_AGREED,
        self::TYPE_BLOCKED,
        self::TYPE_DISABLED,
        self::TYPE_DRAFT,
        self::TYPE_TO_VALIDATE,
        self::TYPE_UNDER_EXEMPTION
    ];

    public function getName(): string {
        return 'product_current_place';
    }
}
